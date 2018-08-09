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
        $node = $treeBuilder->root('bruery_classification');
        $this->addSettingsSection($node);
        $this->addManagerSection($node);
        $this->addProviderSection($node);
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
                        ->arrayNode('category')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('default_context')->cannotBeEmpty()->end()
                            ->end()  #--end category children
                        ->end() #--end category
                        ->arrayNode('collection')
                            ->cannotBeEmpty()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('default_context')->cannotBeEmpty()->end()
                            ->end()  #--end collection children
                        ->end() #--end collection
                        ->arrayNode('tag')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('default_context')->cannotBeEmpty()->end()
                            ->end()  #--end tag children
                        ->end()#--end tag
                    ->end()#--end children settings
                ->end()#--end settings
            ->end()
        ;
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
                                ->scalarNode('tag')->defaultValue('Bruery\\ClassificationBundle\\Entity\\TagManager')->end()
                                ->scalarNode('category')->defaultValue('Bruery\\ClassificationBundle\\Entity\\CategoryManager')->end()
                                ->scalarNode('collection')->defaultValue('Bruery\\ClassificationBundle\\Entity\\CollectionManager')->end()
                                ->scalarNode('context')->defaultValue('Bruery\\ClassificationBundle\\Entity\\ContextManager')->end()
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
                                        ->scalarNode('category')->cannotBeEmpty()->defaultValue('Bruery\\ClassificationBundle\\Provider\\Category\\Pool')->end()
                                        ->scalarNode('collection')->cannotBeEmpty()->defaultValue('Bruery\\ClassificationBundle\\Provider\\Collection\\Pool')->end()
                                        ->scalarNode('tag')->cannotBeEmpty()->defaultValue('Bruery\\ClassificationBundle\\Provider\\Tag\\Pool')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('default_provider')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('category')->cannotBeEmpty()->defaultValue('Bruery\\ClassificationBundle\\Provider\\Category\\DefaultProvider')->end()
                                        ->scalarNode('collection')->cannotBeEmpty()->defaultValue('Bruery\\ClassificationBundle\\Provider\\Collection\\DefaultProvider')->end()
                                        ->scalarNode('tag')->cannotBeEmpty()->defaultValue('Bruery\\ClassificationBundle\\Provider\\Tag\\DefaultProvider')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('category')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('context')
                                    ->useAttributeAsKey('id')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('provider')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('collection')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('context')
                                    ->useAttributeAsKey('id')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('provider')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('tag')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('context')
                                    ->useAttributeAsKey('id')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('provider')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
