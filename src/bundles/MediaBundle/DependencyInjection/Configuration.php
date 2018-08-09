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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('bruery_media');
        $this->addModelSection($node);
        $this->addManagerSection($node);
        $this->addAdminSection($node);
        $this->addBlockSettings($node);
        $this->addProviderSection($node);
        $this->addSettingsSection($node);
        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addSettingsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('slugify_service')
                    ->info('You should use: sonata.core.slugify.cocur, but for BC we keep \'sonata.core.slugify.native\' as default')
                    ->defaultValue('sonata.core.slugify.cocur')
                ->end()
                ->arrayNode('settings')
                    ->cannotBeEmpty()
                    ->children()
                        ->arrayNode('media')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('default_context')->defaultNull()->end()
                                ->scalarNode('hide_context')->defaultValue(false)->end()
                                ->scalarNode('default_category')->defaultNull()->end()
                            ->end()  #--end media children
                        ->end() #--end media
                        ->arrayNode('gallery')
                            ->cannotBeEmpty()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('default_context')->cannotBeEmpty()->end()
                                ->scalarNode('default_collection')->cannotBeEmpty()->end()
                            ->end()  #--end gallery children
                        ->end() #--end gallery
                        ->arrayNode('gallery_has_media')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('media')
                                    ->isRequired()
                                    ->children()
                                        ->scalarNode('default_context')->defaultNull()->end()
                                        ->scalarNode('hide_context')->defaultValue(false)->end()
                                        ->scalarNode('default_category')->defaultNull()->end()
                                    ->end()
                                ->end()  #--end media
                            ->end()  #--end media children
                        ->end()#--end gallery_has_media
                    ->end()#--end children settings
                ->end()#--end settings
            ->end();
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addManagerSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('manager_class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('orm')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('media')->defaultValue('Bruery\\MediaBundle\\Entity\\MediaManager')->end()
                                ->scalarNode('gallery')->defaultValue('Bruery\\MediaBundle\\Entity\\GalleryManager')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

     /**
     * @param ArrayNodeDefinition $node
     */
    private function addModelSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('media')->defaultValue('AppBundle\\Entity\\Media\\Media')->end()
                        ->scalarNode('gallery')->defaultValue('AppBundle\\Entity\\Media\\Gallery')->end()
                        ->scalarNode('gallery_has_media')->defaultValue('AppBundle\\Entity\\Media\\GalleryHasMedia')->end()
                        ->scalarNode('collection')->defaultValue('AppBundle\\Entity\\Classification\\Collection')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addAdminSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('media')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Bruery\\MediaBundle\\Admin\\ORM\\MediaAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('SonataMediaBundle:MediaAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataMediaBundle')->end()
                            ->end()
                        ->end()
                        ->arrayNode('gallery')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Bruery\\MediaBundle\\Admin\\GalleryAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('SonataMediaBundle:GalleryAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataMediaBundle')->end()
                            ->end()
                        ->end()
                        ->arrayNode('gallery_has_media')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Bruery\\MediaBundle\\Admin\\ORM\\GalleryHasMediaAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('SonataAdminBundle:CRUD')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataMediaBundle')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addBlockSettings(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('blocks')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('media')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Bruery\\MediaBundle\\Block\\MediaBlockService')->end()
                                ->arrayNode('templates')
                                    ->useAttributeAsKey('id')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('name')->defaultValue('default')->end()
                                            ->scalarNode('path')->defaultValue('BrueryMediaBundle:Block:block_media.html.twig')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addProviderSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('providers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('class')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('pool')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('gallery')->cannotBeEmpty()->defaultValue('Bruery\\MediaBundle\\Provider\\Gallery\\Pool')->end()
                                        ->scalarNode('gallery_has_media')->cannotBeEmpty()->defaultValue('Bruery\\MediaBundle\\Provider\\GalleryHasMedia\\Pool')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('default_provider')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('gallery')->cannotBeEmpty()->defaultValue('Bruery\\MediaBundle\\Provider\\Gallery\\DefaultProvider')->end()
                                        ->scalarNode('gallery_has_media')->cannotBeEmpty()->defaultValue('Bruery\\MediaBundle\\Provider\\GalleryHasMedia\\DefaultProvider')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('gallery')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('collections')
                                    ->useAttributeAsKey('id')
                                    ->isRequired()
                                    ->prototype('array')
                                        ->children()
                                            ->arrayNode('gallery')
                                             ->addDefaultsIfNotSet()
                                                 ->children()
                                                    ->scalarNode('provider')->end()
                                                 ->end() #--> end children
                                            ->end() #--> post_sets
                                            ->arrayNode('gallery_has_media')
                                             ->addDefaultsIfNotSet()
                                                 ->children()
                                                    ->scalarNode('provider')->end()
                                                    ->arrayNode('settings')
                                                        ->useAttributeAsKey('id')
                                                        ->prototype('array')
                                                            ->children()
                                                                ->arrayNode('params')
                                                                    ->prototype('array')
                                                                        ->children()
                                                                            ->scalarNode('key')->end()
                                                                            ->scalarNode('value')->end()
                                                                        ->end()
                                                                    ->end()
                                                                ->end()
                                                            ->end()
                                                        ->end()
                                                    ->end() #--> end settings
                                                 ->end() #--> end children
                                            ->end() #--> end gallery_has_media
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
