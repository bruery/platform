<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2017. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\EntityAuditBundle\EventListener;

use Bruery\EntityAuditBundle\Model\AuditManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;

class LogRevisionsListener implements EventSubscriber
{
    /**
     * @var \SimpleThings\EntityAudit\AuditConfiguration
     */
    protected $config;

    /**
     * @var \SimpleThings\EntityAudit\Metadata\MetadataFactory
     */
    protected $metadataFactory;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $conn;

    /**
     * @var \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    protected $platform;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $sourceEm;

    /**
     * @var array
     */
    protected $insertRevisionSQL = array();

    /**
     * @var \Doctrine\ORM\UnitOfWork
     */
    protected $uow;

    /**
     * @var int
     */
    protected $revisionId;

    protected $auditEm;

    public function __construct(AuditManager $auditManager)
    {
        $this->config = $auditManager->getConfiguration();
        $this->metadataFactory = $auditManager->getMetadataFactory();
    }

    public function getSubscribedEvents()
    {
        return array(Events::onFlush, Events::postPersist, Events::postUpdate);
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        // onFlush was executed before, everything already initialized
        $entity = $eventArgs->getEntity();

        $class = $this->sourceEm->getClassMetadata(get_class($entity));
        if (!$this->metadataFactory->isAudited($class->name)) {
            return;
        }

        $this->saveRevisionEntityData($class, $this->getOriginalEntityData($entity), 'INS');
    }

    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        // onFlush was executed before, everything already initialized
        $entity = $eventArgs->getEntity();

        $class = $this->sourceEm->getClassMetadata(get_class($entity));
        if (!$this->metadataFactory->isAudited($class->name)) {
            return;
        }

        // get changes => should be already computed here (is a listener)
        $changeset = $this->uow->getEntityChangeSet($entity);
        foreach ($this->config->getGlobalIgnoreColumns() as $column) {
            if (isset($changeset[$column])) {
                unset($changeset[$column]);
            }
        }

        // if we have no changes left => don't create revision log
        if (count($changeset) == 0) {
            return;
        }

        $entityData = array_merge($this->getOriginalEntityData($entity), $this->uow->getEntityIdentifier($entity));
        $this->saveRevisionEntityData($class, $entityData, 'UPD');
    }

    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $this->sourceEm = $eventArgs->getEntityManager();
        $this->conn = $this->sourceEm->getConnection();
        $this->uow = $this->sourceEm->getUnitOfWork();
        $this->platform = $this->conn->getDatabasePlatform();
        $this->revisionId = null; // reset revision

        foreach ($this->uow->getScheduledEntityDeletions() as $entity) {
            $class = $this->sourceEm->getClassMetadata(get_class($entity));
            if (!$this->metadataFactory->isAudited($class->name)) {
                continue;
            }
            $entityData = array_merge($this->getOriginalEntityData($entity), $this->uow->getEntityIdentifier($entity));
            $this->saveRevisionEntityData($class, $entityData, 'DEL');
        }
    }

    /**
     * get original entity data, including versioned field, if "version" constraint is used
     *
     * @param mixed $entity
     * @return array
     */
    protected function getOriginalEntityData($entity)
    {
        $class = $this->sourceEm->getClassMetadata(get_class($entity));
        $data = $this->uow->getOriginalEntityData($entity);
        if ($class->isVersioned) {
            $versionField = $class->versionField;
            $data[$versionField] = $class->reflFields[$versionField]->getValue($entity);
        }
        return $data;
    }

    protected function getRevisionId()
    {
        if ($this->revisionId === null) {
            $this->auditEm->getConnection()->insert($this->config->getRevisionTableName(), array(
                'timestamp'     => date_create('now'),
                'username'      => $this->config->getCurrentUsername() ?: 'admin',
            ), array(
                Type::DATETIME,
                Type::STRING
            ));

            $sequenceName = $this->platform->supportsSequences()
                ? 'REVISIONS_ID_SEQ'
                : null;

            $this->revisionId = $this->auditEm->getConnection()->lastInsertId($sequenceName);
        }
        return $this->revisionId;
    }

    protected function getInsertRevisionSQL($class)
    {
        if (!isset($this->insertRevisionSQL[$class->name])) {
            $placeholders = array('?', '?');
            $tableName    = $this->config->getTablePrefix() . $class->table['name'] . $this->config->getTableSuffix();

            $sql = "INSERT INTO " . $tableName . " (" .
                    $this->config->getRevisionFieldName() . ", " . $this->config->getRevisionTypeFieldName();

            $fields = array();

            foreach ($class->associationMappings as $assoc) {
                if (($assoc['type'] & ClassMetadata::TO_ONE) > 0 && $assoc['isOwningSide']) {
                    foreach ($assoc['targetToSourceKeyColumns'] as $sourceCol) {
                        $fields[$sourceCol] = true;
                        $sql .= ', ' . $sourceCol;
                        $placeholders[] = '?';
                    }
                }
            }

            foreach ($class->fieldNames as $field) {
                if (array_key_exists($field, $fields)) {
                    continue;
                }
                $type = Type::getType($class->fieldMappings[$field]['type']);
                $placeholders[] = (!empty($class->fieldMappings[$field]['requireSQLConversion']))
                    ? $type->convertToDatabaseValueSQL('?', $this->platform)
                    : '?';
                $sql .= ', ' . $class->getQuotedColumnName($field, $this->platform);
            }

            $sql .= ") VALUES (" . implode(", ", $placeholders) . ")";
            $this->insertRevisionSQL[$class->name] = $sql;
        }

        return $this->insertRevisionSQL[$class->name];
    }

    /**
     * @param ClassMetadata $class
     * @param array $entityData
     * @param string $revType
     */
    protected function saveRevisionEntityData($class, $entityData, $revType)
    {
        $params = array($this->getRevisionId(), $revType);
        $types = array(\PDO::PARAM_INT, \PDO::PARAM_STR);

        $fields = array();

        foreach ($class->associationMappings as $field => $assoc) {
            if (($assoc['type'] & ClassMetadata::TO_ONE) > 0 && $assoc['isOwningSide']) {
                $targetClass = $this->sourceEm->getClassMetadata($assoc['targetEntity']);

                if ($entityData[$field] !== null) {
                    $relatedId = $this->uow->getEntityIdentifier($entityData[$field]);
                }

                $targetClass = $this->sourceEm->getClassMetadata($assoc['targetEntity']);

                foreach ($assoc['sourceToTargetKeyColumns'] as $sourceColumn => $targetColumn) {
                    $fields[$sourceColumn] = true;
                    if ($entityData[$field] === null) {
                        $params[] = null;
                        $types[] = \PDO::PARAM_STR;
                    } else {
                        $params[] = $relatedId[$targetClass->fieldNames[$targetColumn]];
                        $types[] = $targetClass->getTypeOfColumn($targetColumn);
                    }
                }
            }
        }

        foreach ($class->fieldNames as $field) {
            if (array_key_exists($field, $fields)) {
                continue;
            }
            $params[] = $entityData[$field];
            $types[] = $class->fieldMappings[$field]['type'];
        }

        $this->auditEm->getConnection()->executeUpdate($this->getInsertRevisionSQL($class), $params, $types);
    }

    /**
     * @return mixed
     */
    public function getAuditEm()
    {
        return $this->auditEm;
    }

    /**
     * @param mixed $auditEm
     */
    public function setAuditEm($auditEm)
    {
        $this->auditEm = $auditEm;
    }
}
