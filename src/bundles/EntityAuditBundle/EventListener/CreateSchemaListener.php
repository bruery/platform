<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamorw <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\EntityAuditBundle\EventListener;

use Bruery\EntityAuditBundle\Model\AuditManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

class CreateSchemaListener implements EventSubscriber
{
    /**
     * @var \SimpleThings\EntityAudit\AuditConfiguration
     */
    protected $config;

    /**
     * @var \SimpleThings\EntityAudit\Metadata\MetadataFactory
     */
    protected $metadataFactory;

    public function __construct(AuditManager $auditManager)
    {
        $this->config = $auditManager->getConfiguration();
        $this->metadataFactory = $auditManager->getMetadataFactory();
    }

    public function getSubscribedEvents()
    {
        return array(
            ToolEvents::postGenerateSchemaTable,
            ToolEvents::postGenerateSchema,
        );
    }

    public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $eventArgs)
    {
        $cm = $eventArgs->getClassMetadata();
        if ($this->metadataFactory->isAudited($cm->name)) {
            $schema = $eventArgs->getSchema();

            $entityTable = $eventArgs->getClassTable();

            $revisionTable = $schema->createTable(
                $this->config->getTablePrefix().$entityTable->getName().$this->config->getTableSuffix()
            );
            foreach ($entityTable->getColumns() as $column) {
                /* @var $column Column */
                $revisionTable->addColumn($column->getName(), $column->getType()->getName(), array_merge(
                    $column->toArray(),
                    array('notnull' => false, 'autoincrement' => false)
                ));
            }
            $revisionTable->addColumn($this->config->getRevisionFieldName(), $this->config->getRevisionIdFieldType());
            $revisionTable->addColumn($this->config->getRevisionTypeFieldName(), 'string', array('length' => 4));
            $pkColumns = $entityTable->getPrimaryKey()->getColumns();
            $pkColumns[] = $this->config->getRevisionFieldName();
            $revisionTable->setPrimaryKey($pkColumns);
        }
    }

    public function postGenerateSchema(GenerateSchemaEventArgs $eventArgs)
    {
        $schema = $eventArgs->getSchema();

        $revisionsTable = $schema->createTable($this->config->getRevisionTableName());
        $revisionsTable->addColumn('id', $this->config->getRevisionIdFieldType(), array(
            'autoincrement' => true,
        ));
        $revisionsTable->addColumn('timestamp', 'datetime');
        $revisionsTable->addColumn('username', 'string');
        $revisionsTable->setPrimaryKey(array('id'));
    }
}
