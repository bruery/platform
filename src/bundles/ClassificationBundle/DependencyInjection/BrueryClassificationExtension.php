<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\ClassificationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class BrueryClassificationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('twig.xml');
        $this->configureManagerClass($config, $container);
        $this->configureSettings($config, $container);
        $loader->load('provider.xml');
        $this->configureProviders($config, $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureSettings($config, ContainerBuilder $container)
    {
        $container->setParameter('bruery.classification.slugify_service', $config['slugify_service']);

        $container->setParameter('bruery.classification.category.default_context',    $config['settings']['category']['default_context']);
        $container->setParameter('bruery.classification.collection.default_context',  $config['settings']['collection']['default_context']);
        $container->setParameter('bruery.classification.tag.default_context',         $config['settings']['tag']['default_context']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureManagerClass($config, ContainerBuilder $container)
    {
        $container->setParameter('bruery.classification.entity.manager.tag.class',        $config['manager_class']['orm']['tag']);
        $container->setParameter('bruery.classification.entity.manager.category.class',   $config['manager_class']['orm']['category']);
        $container->setParameter('bruery.classification.entity.manager.collection.class', $config['manager_class']['orm']['collection']);
        $container->setParameter('bruery.classification.entity.manager.context.class',    $config['manager_class']['orm']['context']);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $config
     */
    public function configureProviders($config, ContainerBuilder $container)
    {
        #Category Provider
        $categoryPool = $container->getDefinition('bruery.classification.category.pool');
        $categoryPool->replaceArgument(0, $config['settings']['category']['default_context']);

        $container->setParameter('bruery.classification.category.provider.context',                     $config['providers']['category']['context']);


        #Collection Provider
        $collectionPool = $container->getDefinition('bruery.classification.collection.pool');
        $collectionPool->replaceArgument(0, $config['settings']['collection']['default_context']);

        $container->setParameter('bruery.classification.collection.provider.context',                   $config['providers']['collection']['context']);

        #Tag Provider
        $collectionPool = $container->getDefinition('bruery.classification.tag.pool');
        $collectionPool->replaceArgument(0, $config['settings']['tag']['default_context']);

        $container->setParameter('bruery.classification.tag.provider.context',                          $config['providers']['tag']['context']);
    }
}
