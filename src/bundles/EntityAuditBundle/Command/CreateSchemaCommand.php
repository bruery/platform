<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
<<<<<<< HEAD
 * (c) Mell Zamora <mellzamora@outlook.com>
=======
 * (c) Mell Zamorw <mellzamora@outlook.com>
>>>>>>> 0b04c6d05740dadde1a0b9626514bbf0ebbed3d5
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\EntityAuditBundle\Command;

use Bruery\EntityAuditBundle\Tools\SchemaTool;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand as BaseCreateCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to execute the SQL needed to generate the database schema for
 * a given entity manager.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class CreateSchemaCommand extends BaseCreateCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('audit:schema:create')
            ->setDescription('Executes (or dumps) the SQL needed to generate the database schema')
            ->setDefinition(array(
                new InputOption(
                    'dump-sql', null, InputOption::VALUE_NONE,
                    'Instead of try to apply generated SQLs into EntityManager Storage Connection, output them.'
                )
            ))
            ->setHelp(<<<EOT
Processes the schema and either create it directly on EntityManager Storage Connection or generate the SQL output.

<comment>Hint:</comment> If you have a database with tables that should not be managed
by the ORM, you can use a DBAL functionality to filter the tables and sequences down
on a global level:

    \$config->setFilterSchemaAssetsExpression(\$regexp);
EOT
            )
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
    }

    /**
     * {@inheritdoc}
     */
    protected function execSchemaCommand(InputInterface $input, OutputInterface $output, SchemaTool $schemaTool, array $metadatas)
    {
        if ($input->getOption('dump-sql')) {
            $sqls = $schemaTool->getCreateSchemaSql($metadatas);
            $output->writeln(implode(';' . PHP_EOL, $sqls) . ';');
        } else {
            $output->writeln('ATTENTION: This operation should not be executed in a production environment.' . PHP_EOL);
            $output->writeln('Creating database schema...');
            $schemaTool->createSchema($metadatas);
            $output->writeln('Database schema created successfully!');
        }

        return 0;
    }
}
