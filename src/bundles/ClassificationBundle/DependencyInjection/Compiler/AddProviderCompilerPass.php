<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\ClassificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddProviderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->attachProviders($container);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function attachProviders(ContainerBuilder $container)
    {
        ########################
        # Category Provider
        ########################
        $pool = $container->getDefinition('bruery.classification.category.pool');

        foreach ($container->findTaggedServiceIds('bruery.classification.category.provider') as $id => $attributes) {
            $pool->addMethodCall('addProvider', array($id, new Reference($id)));
        }

        $contexts = $container->getParameter('bruery.classification.category.provider.context');

        foreach ($contexts as $name => $settings) {
            $pool->addMethodCall('addContext', array($name, $settings['provider']));
        }

        ########################
        # Collection Provider
        ########################
        $pool = $container->getDefinition('bruery.classification.collection.pool');

        foreach ($container->findTaggedServiceIds('bruery.classification.collection.provider') as $id => $attributes) {
            $pool->addMethodCall('addProvider', array($id, new Reference($id)));
        }

        $contexts = $container->getParameter('bruery.classification.collection.provider.context');

        foreach ($contexts as $name => $settings) {
            $pool->addMethodCall('addContext', array($name, $settings['provider']));
        }

        ########################
        # Tag Provider
        ########################
        $pool = $container->getDefinition('bruery.classification.tag.pool');

        foreach ($container->findTaggedServiceIds('bruery.classification.tag.provider') as $id => $attributes) {
            $pool->addMethodCall('addProvider', array($id, new Reference($id)));
        }

        $contexts = $container->getParameter('bruery.classification.tag.provider.context');

        foreach ($contexts as $name => $settings) {
            $pool->addMethodCall('addContext', array($name, $settings['provider']));
        }
    }
}
