<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\MediaBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BrueryMediaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('provider.xml');
        $loader->load('block.xml');

        $this->configureManagerClass($config, $container);
        $this->configureAdminClass($config, $container);
        $this->configureBlocks($config['blocks'], $container);
        $this->configureProviders($container, $config);
        $this->configureSettings($container, $config);
        $this->registerDoctrineMapping($config);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureSettings(ContainerBuilder $container, $config)
    {
        $container->setParameter('bruery.media.slugify_service',                    $config['slugify_service']);
        $container->setParameter('bruery.media.settings.media',                     $config['settings']['media']);
        $container->setParameter('bruery.media.settings.gallery',                   $config['settings']['gallery']);
        $container->setParameter('bruery.media.settings.gallery_has_media',         $config['settings']['gallery_has_media']);

        $container->setParameter('bruery.media.gallery.default_context',             $config['settings']['gallery']['default_context']);
        $container->setParameter('bruery.media.gallery.default_collection',          $config['settings']['gallery']['default_collection']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureManagerClass($config, ContainerBuilder $container)
    {
        $container->setParameter('bruery.media.entity.manager.media.class',             $config['manager_class']['orm']['media']);
        $container->setParameter('bruery.media.entity.manager.gallery.class',           $config['manager_class']['orm']['gallery']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureAdminClass($config, ContainerBuilder $container)
    {
        $container->setParameter('bruery.media.admin.media.class',              $config['admin']['media']['class']);
        $container->setParameter('bruery.media.admin.media.controller',         $config['admin']['media']['controller']);
        $container->setParameter('bruery.media.admin.media.translation_domain', $config['admin']['media']['translation']);

        $container->setParameter('bruery.media.admin.gallery.class',              $config['admin']['gallery']['class']);
        $container->setParameter('bruery.media.admin.gallery.controller',         $config['admin']['gallery']['controller']);
        $container->setParameter('bruery.media.admin.gallery.translation_domain', $config['admin']['gallery']['translation']);

        $container->setParameter('bruery.media.admin.gallery_has_media.class',              $config['admin']['gallery_has_media']['class']);
        $container->setParameter('bruery.media.admin.gallery_has_media.controller',         $config['admin']['gallery_has_media']['controller']);
        $container->setParameter('bruery.media.admin.gallery_has_media.translation_domain', $config['admin']['gallery_has_media']['translation']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureBlocks($config, ContainerBuilder $container)
    {
        ####################################
        # bruery.media.block.media
        ####################################

        # class
        $container->setParameter('bruery.media.block.media.class', $config['media']['class']);
        # template
        if ($temp = $config['media']['templates']) {
            $templates = array();
            foreach ($temp as $template) {
                $templates[$template['path']] = $template['name'];
            }
            $container->setParameter('bruery.media.block.media.templates', $templates);
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $config
     */
    public function configureProviders(ContainerBuilder $container, $config)
    {
        $container->setParameter('bruery.media.gallery.pool.class',                         $config['providers']['class']['pool']['gallery']);
        $container->setParameter('bruery.media.gallery_has_media.pool.class',               $config['providers']['class']['pool']['gallery_has_media']);
        $container->setParameter('bruery.media.gallery_provider.default.class',             $config['providers']['class']['default_provider']['gallery']);
        $container->setParameter('bruery.media.gallery_has_media_provider.default.class',   $config['providers']['class']['default_provider']['gallery_has_media']);

        $galleryPool = $container->getDefinition('bruery.media.gallery.pool');
        $galleryPool->replaceArgument(0, $config['settings']['gallery']['default_collection']);

        $galleryHasMediaPool = $container->getDefinition('bruery.media.gallery_has_media.pool');
        $galleryHasMediaPool->replaceArgument(0, $config['settings']['gallery']['default_collection']);

        $container->setParameter('bruery.media.gallery.provider.collections',        $config['providers']['gallery']['collections']);
    }

    /**
     * @param array $config
     */
    public function registerDoctrineMapping(array $config)
    {
        $collector = DoctrineCollector::getInstance();

        if (interface_exists('Sonata\ClassificationBundle\Model\CollectionInterface')) {
            $collector->addAssociation($config['class']['gallery'], 'mapManyToOne', array(
                'fieldName'     => 'collection',
                'targetEntity'  => $config['class']['collection'],
                'cascade'       => array(
                    'persist',
                ),
                'mappedBy'      => null,
                'inversedBy'    => null,
                'joinColumns'   => array(
                    array(
                        'name'                 => 'collection_id',
                        'referencedColumnName' => 'id',
                        'onDelete'             => 'SET NULL',
                    ),
                ),
                'orphanRemoval' => false,
            ));
        }
    }
}
