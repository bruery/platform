<?php

/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamorw <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\EntityAuditBundle\Command;

use Bruery\EntityAuditBundle\Tools\SchemaTool;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaDoctrineCommand as BaseUpdateCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to generate the SQL needed to update the database schema to match
 * the current mapping information.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class UpdateSchemaCommand extends BaseUpdateCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('audit:schema:update')
            ->addOption('source-em', null, InputOption::VALUE_OPTIONAL, 'Source entity manager to use for this command')
            ->addOption('audit-em', null, InputOption::VALUE_OPTIONAL, 'Audit entity manager to use for this command');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $application = $this->getApplication();

        #source
        $source = $input->getOption('source-em') ?: 'default';
        $emSource = $application->getKernel()->getContainer()->get('doctrine')->getManager($source);
        $helperSet = $application->getHelperSet();
        $helperSet->set(new ConnectionHelper($emSource->getConnection()), 'source-db');
        $helperSet->set(new EntityManagerHelper($emSource), 'source-em');


        #audit
        $audit = $input->getOption('audit-em') ?: 'audit';
        $emAudit = $application->getKernel()->getContainer()->get('doctrine')->getManager($audit);
        $helperSet->set(new ConnectionHelper($emAudit->getConnection()), 'audit-db');
        $helperSet->set(new EntityManagerHelper($emAudit), 'audit-em');

        $sourceEmHelper = $this->getHelper('source-em');
        $auditEmHelper = $this->getHelper('audit-em');

        /* @var $em \Doctrine\ORM\EntityManager */
        $sourceEm = $sourceEmHelper->getEntityManager();
        $auditEm = $auditEmHelper->getEntityManager();

        $sourceMetadatas = $sourceEm->getMetadataFactory()->getAllMetadata();


        if (! empty($sourceMetadatas)) {
            // Create SchemaTool
            $auditTool  = new SchemaTool($auditEm, $sourceEm);
            return $this->execSchemaCommand($input, $output, $auditTool, $sourceMetadatas);
        } else {
            $output->writeln('No Metadata Classes to process.');
            return 0;
        }

        return parent::execute($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function execSchemaCommand(InputInterface $input, OutputInterface $output, SchemaTool $schemaTool, array $metadatas)
    {
        // Defining if update is complete or not (--complete not defined means $saveMode = true)
        $saveMode = ! $input->getOption('complete');

        $sqls = $schemaTool->getUpdateSchemaSql($metadatas, $saveMode);

        if (0 === count($sqls)) {
            $output->writeln('Nothing to update - your database is already in sync with the current entity metadata.');

            return 0;
        }

        $dumpSql = true === $input->getOption('dump-sql');
        $force   = true === $input->getOption('force');

        if ($dumpSql) {
            $output->writeln(implode(';' . PHP_EOL, $sqls) . ';');
        }

        if ($force) {
            if ($dumpSql) {
                $output->writeln('');
            }
            $output->writeln('Updating database schema...');
            $schemaTool->updateSchema($metadatas, $saveMode);
            $output->writeln(sprintf('Database schema updated successfully! "<info>%s</info>" queries were executed', count($sqls)));
        }

        if ($dumpSql || $force) {
            return 0;
        }

        $output->writeln('<comment>ATTENTION</comment>: This operation should not be executed in a production environment.');
        $output->writeln('           Use the incremental update to detect changes during development and use');
        $output->writeln('           the SQL DDL provided to manually update your database in production.');
        $output->writeln('');

        $output->writeln(sprintf('The Schema-Tool would execute <info>"%s"</info> queries to update the database.', count($sqls)));
        $output->writeln('Please run the operation by passing one - or both - of the following options:');

        $output->writeln(sprintf('    <info>%s --force</info> to execute the command', $this->getName()));
        $output->writeln(sprintf('    <info>%s --dump-sql</info> to dump the SQL statements to the screen', $this->getName()));

        return 1;
    }
}
