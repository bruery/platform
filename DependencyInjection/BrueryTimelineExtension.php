<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\TimelineBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BrueryTimelineExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('orm.xml');
        $loader->load('admin.xml');

        $this->configureManagerClass($config, $container);
        $this->configureClass($config, $container);
        $this->configureAdminClass($config, $container);
        $this->configureController($config, $container);
        $this->configureTranslationDomain($config, $container);
        $this->configureBlock($config, $container);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureBlock($config, ContainerBuilder $container)
    {
        $container->setParameter('bruery.timeline.block.timeline.class', $config['block']['timeline']['class']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureClass($config, ContainerBuilder $container)
    {
        $container->setParameter('bruery.timeline.admin.timeline.entity', $config['class']['timeline']);
        $container->setParameter('bruery.timeline.timeline.entity',       $config['class']['timeline']);

        $container->setParameter('bruery.timeline.admin.action.entity', $config['class']['action']);
        $container->setParameter('bruery.timeline.action.entity',       $config['class']['action']);

        $container->setParameter('bruery.timeline.admin.action_component.entity', $config['class']['action_component']);
        $container->setParameter('bruery.timeline.action_component.entity',       $config['class']['action_component']);

        $container->setParameter('bruery.timeline.admin.component.entity', $config['class']['component']);
        $container->setParameter('bruery.timeline.component.entity',       $config['class']['component']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureManagerClass($config, ContainerBuilder $container)
    {
        $container->setParameter('bruery.timeline.entity.manager.timeline.class',             $config['manager_class']['orm']['timeline']);
        $container->setParameter('bruery.timeline.entity.manager.action.class',               $config['manager_class']['orm']['action']);
        $container->setParameter('bruery.timeline.entity.manager.action_component.class',     $config['manager_class']['orm']['action_component']);
        $container->setParameter('bruery.timeline.entity.manager.component.class',            $config['manager_class']['orm']['component']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureAdminClass($config, ContainerBuilder $container)
    {
        $container->setParameter('bruery.timeline.admin.timeline.class',              $config['admin']['timeline']['class']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureTranslationDomain($config, ContainerBuilder $container)
    {
        $container->setParameter('bruery.timeline.admin.timeline.translation_domain', $config['admin']['timeline']['translation']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureController($config, ContainerBuilder $container)
    {
        $container->setParameter('bruery.timeline.admin.timeline.controller',         $config['admin']['timeline']['controller']);
    }
}
