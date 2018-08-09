<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\MediaBundle\DependencyInjection\Compiler;

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
        $definition = $container->getDefinition('sonata.media.manager.media');
        $definition->setClass($container->getParameter('bruery.media.entity.manager.media.class'));

        $definition = $container->getDefinition('sonata.media.manager.gallery');
        $definition->setClass($container->getParameter('bruery.media.entity.manager.gallery.class'));


        #####################################
        ## Override Media Admin
        #####################################
        $definition = $container->getDefinition('sonata.media.admin.media');
        $definition->setClass($container->getParameter('bruery.media.admin.media.class'));
        $definition->addMethodCall('setTranslationDomain', array($container->getParameter('bruery.media.admin.media.translation_domain')));
        $definition->addMethodCall('setBaseControllerName', array($container->getParameter('bruery.media.admin.media.controller')));


        #####################################
        ## Override Gallery Admin
        #####################################
        $definition = $container->getDefinition('sonata.media.admin.gallery');
        $definition->setClass($container->getParameter('bruery.media.admin.gallery.class'));
        $definition->addMethodCall('setTranslationDomain', array($container->getParameter('bruery.media.admin.gallery.translation_domain')));
        $definition->addMethodCall('setBaseControllerName', array($container->getParameter('bruery.media.admin.gallery.controller')));
        $definition->addMethodCall('setCollectionManager', array(new Reference('sonata.classification.manager.collection')));
        $definition->addMethodCall('setContextManager', array(new Reference('sonata.classification.manager.context')));
        $definition->addMethodCall('setCategoryManager', array(new Reference('sonata.classification.manager.category')));
        $definition->addMethodCall('setGalleryPool', array(new Reference('bruery.media.gallery.pool')));
        $definition->addMethodCall('setChildGalleryPool', array(new Reference('bruery.media.gallery_has_media.pool')));
        $definition->addMethodCall('setDefaultContext', array($container->getParameter('bruery.media.gallery.default_context')));
        $definition->addMethodCall('setDefaultCollection', array($container->getParameter('bruery.media.gallery.default_collection')));
        $definition->addMethodCall('setSettings', array($container->getParameter('bruery.media.settings.gallery')));


        #set slugify service
        $serviceId = $container->getParameter('bruery.media.slugify_service');
        $definition->addMethodCall('setSlugify', array(new Reference($serviceId)));

        #####################################
        ## Override Gallery_Has_Media Admin
        #####################################
        $definition = $container->getDefinition('sonata.media.admin.gallery_has_media');
        $definition->setClass($container->getParameter('bruery.media.admin.gallery_has_media.class'));
        $definition->addMethodCall('setTranslationDomain', array($container->getParameter('bruery.media.admin.gallery_has_media.translation_domain')));
        $definition->addMethodCall('setBaseControllerName', array($container->getParameter('bruery.media.admin.gallery_has_media.controller')));
        $definition->addMethodCall('setCollectionManager', array(new Reference('sonata.classification.manager.collection')));
        $definition->addMethodCall('setContextManager', array(new Reference('sonata.classification.manager.context')));
        $definition->addMethodCall('setCategoryManager', array(new Reference('sonata.classification.manager.category')));
        $definition->addMethodCall('setPool', array(new Reference('bruery.media.gallery_has_media.pool')));
        $definition->addMethodCall('setDefaultContext', array($container->getParameter('bruery.media.gallery.default_context')));
        $definition->addMethodCall('setDefaultCollection', array($container->getParameter('bruery.media.gallery.default_collection')));
        $definition->addMethodCall('setSettings', array($container->getParameter('bruery.media.settings.gallery_has_media')));
        $definition->addMethodCall('setSlugify', array(new Reference($serviceId)));

        ##############################
        # Override File Provider Class
        ##############################
        $definition = $container->getDefinition('sonata.media.provider.file');
        $definition->setClass($container->getParameter('bruery.media.provider.file.class'));

        ##############################
        # Override Image Provider Class
        ##############################
        $definition = $container->getDefinition('sonata.media.provider.image');
        $definition->setClass($container->getParameter('bruery.media.provider.image.class'));

        #########################################
        # BLOCK add bruery.media.block.media template
        #########################################
        $definition = $container->getDefinition('bruery.media.block.media');
        if ($container->hasParameter('bruery.media.block.media.templates')) {
            $definition->addMethodCall('setTemplates', array($container->getParameter('bruery.media.block.media.templates')));
        }
    }
}
