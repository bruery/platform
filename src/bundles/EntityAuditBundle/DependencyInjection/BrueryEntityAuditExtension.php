<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamorw <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\EntityAuditBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BrueryEntityAuditExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $this->configureSettings($config['settings']['audit'], $container);
        $this->configureEntityManagers($config['settings']['entity_managers'], $container);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureEntityManagers($config, ContainerBuilder $container)
    {
        $container->setParameter('bruery.entity_audit.entity_managers',   $config);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureSettings($config, ContainerBuilder $container)
    {
        $container->setParameter('bruery.entity_audit.manager.class',                        $config['manager']['class']);
        $container->setParameter('bruery.entity_audit.reader.class',                         $config['reader']['class']);

        $container->setParameter('bruery.entity_audit.listener.log_revisions.class',         $config['listener']['log_revisions']['class']);
        $container->setParameter('bruery.entity_audit.listener.log_revisions.connection',    $config['listener']['log_revisions']['connection']);

        $container->setParameter('bruery.entity_audit.listener.create_schema.class',         $config['listener']['create_schema']['class']);
        $container->setParameter('bruery.entity_audit.listener.create_schema.connection',    $config['listener']['create_schema']['connection']);

        $container->setParameter('bruery.entity_audit.config.class',                         $config['config']['class']);
    }
}
