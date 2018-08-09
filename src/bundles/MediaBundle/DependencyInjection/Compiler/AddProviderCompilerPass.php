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
        #set slugify service
        $serviceId = $container->getParameter('bruery.media.slugify_service');

        $galleryPool = $container->getDefinition('bruery.media.gallery.pool');
        $galleryPool->addMethodCall('setSlugify', array(new Reference($serviceId)));

        $galleryHasMediaPool = $container->getDefinition('bruery.media.gallery_has_media.pool');
        $galleryHasMediaPool->addMethodCall('setSlugify', array(new Reference($serviceId)));


        foreach ($container->findTaggedServiceIds('bruery.media.gallery_provider') as $id => $attributes) {
            $galleryPool->addMethodCall('addProvider', array($id, new Reference($id)));
        }

        foreach ($container->findTaggedServiceIds('bruery.media.gallery_has_media_provider') as $id => $attributes) {
            $galleryHasMediaPool->addMethodCall('addProvider', array($id, new Reference($id)));
        }

        $collections = $container->getParameter('bruery.media.gallery.provider.collections');

        foreach ($collections as $name => $settings) {
            if ($settings['gallery']['provider']) {
                $galleryPool->addMethodCall('addCollection', array($name, $settings['gallery']['provider'], array()));
                if ($container->hasDefinition($settings['gallery']['provider'])) {
                    $provider = $container->getDefinition($settings['gallery']['provider']);
                    $provider->addMethodCall('setSlugify', array(new Reference($serviceId)));
                }
            }

            if ($settings['gallery_has_media']['provider']) {
                $galleryHasMediaPool->addMethodCall('addCollection', array($name, $settings['gallery_has_media']['provider'], $settings['gallery_has_media']['settings']));
                if ($container->hasDefinition($settings['gallery_has_media']['provider'])) {
                    $provider =$container->getDefinition($settings['gallery_has_media']['provider']);
                    $provider->addMethodCall('setSlugify', array(new Reference($serviceId)));
                    $provider->addMethodCall('setCategoryManager', array(new Reference('sonata.classification.manager.category')));
                }
            }
        }
    }
}
