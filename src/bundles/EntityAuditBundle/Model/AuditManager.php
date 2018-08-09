<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamorw <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\EntityAuditBundle\Model;

use Bruery\EntityAudit\EventListener\CreateSchemaListener;
use Bruery\EntityAudit\EventListener\LogRevisionsListener;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;

/**
 * Audit Manager grants access to metadata and configuration
 * and has a factory method for audit queries.
 */
class AuditManager
{
    protected $config;

    protected $metadataFactory;

    /**
     * @param AuditConfiguration $config
     */
    public function __construct(AuditConfiguration $config)
    {
        $this->config = $config;
        $this->metadataFactory = $config->createMetadataFactory();
    }

    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    public function getConfiguration()
    {
        return $this->config;
    }

    public function createAuditReader(EntityManager $sourceEm, EntityManager $auditEm)
    {
        return new AuditReader($sourceEm, $auditEm, $this->config, $this->metadataFactory);
    }

    public function registerEvents(EventManager $evm)
    {
        $evm->addEventSubscriber(new CreateSchemaListener($this));
        $evm->addEventSubscriber(new LogRevisionsListener($this));
    }
}
