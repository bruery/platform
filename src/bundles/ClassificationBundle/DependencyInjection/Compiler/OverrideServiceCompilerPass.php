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

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        #####################################
        ## Override Entity Manager
        #####################################

        $serviceId = $container->getParameter('bruery.classification.slugify_service');


        $definition = $container->getDefinition('sonata.classification.manager.tag');
        $definition->setClass($container->getParameter('bruery.classification.entity.manager.tag.class'));

        $definition = $container->getDefinition('sonata.classification.manager.category');
        $definition->setClass($container->getParameter('bruery.classification.entity.manager.category.class'));
        $definition->addMethodCall('setSlugify', array(new Reference($serviceId)));

        $definition = $container->getDefinition('sonata.classification.manager.collection');
        $definition->setClass($container->getParameter('bruery.classification.entity.manager.collection.class'));
        $definition->addMethodCall('setSlugify', array(new Reference($serviceId)));

        $definition = $container->getDefinition('sonata.classification.manager.context');
        $definition->setClass($container->getParameter('bruery.classification.entity.manager.context.class'));
        $definition->addMethodCall('setSlugify', array(new Reference($serviceId)));


        #####################################
        ## Override Collection Admin
        #####################################
        $definition = $container->getDefinition('sonata.classification.admin.category');
        $definition->addMethodCall('setContextManager', array(new Reference('sonata.classification.manager.context')));
        $definition->addMethodCall('setPool', array(new Reference('bruery.classification.category.pool')));
        $definition->addMethodCall('setDefaultContext', array($container->getParameter('bruery.classification.category.default_context')));
        $definition->addMethodCall('setSlugify', array(new Reference($serviceId)));

        #####################################
        ## Override Collection Admin
        #####################################
        $definition = $container->getDefinition('sonata.classification.admin.collection');
        $definition->addMethodCall('setContextManager', array(new Reference('sonata.classification.manager.context')));
        $definition->addMethodCall('setPool', array(new Reference('bruery.classification.collection.pool')));
        $definition->addMethodCall('setDefaultContext', array($container->getParameter('bruery.classification.collection.default_context')));
        $definition->addMethodCall('setSlugify', array(new Reference($serviceId)));

        #####################################
        ## Override Tag Admin
        #####################################
        $definition = $container->getDefinition('sonata.classification.admin.tag');
        $definition->addMethodCall('setContextManager', array(new Reference('sonata.classification.manager.context')));
        $definition->addMethodCall('setPool', array(new Reference('bruery.classification.tag.pool')));
        $definition->addMethodCall('setDefaultContext', array($container->getParameter('bruery.classification.tag.default_context')));
        $definition->addMethodCall('setSlugify', array(new Reference($serviceId)));
    }
}
