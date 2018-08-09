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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bruery_timeline');

        $rootNode
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('timeline')->defaultValue('App\\TimelineBundle\\Entity\\Timeline')->end()
                        ->scalarNode('action')->defaultValue('App\\TimelineBundle\\Entity\\Action')->end()
                        ->scalarNode('action_component')->defaultValue('App\\TimelineBundle\\Entity\\ActionComponent')->end()
                        ->scalarNode('component')->defaultValue('App\\TimelineBundle\\Entity\\Component')->end()
                    ->end()
                ->end()
                ->arrayNode('manager_class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('orm')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('timeline')->defaultValue('Bruery\\TimelineBundle\\Entity\\TimelineManager')->end()
                                ->scalarNode('action')->defaultValue('Bruery\\TimelineBundle\\Entity\\ActionManager')->end()
                                ->scalarNode('action_component')->defaultValue('Bruery\\TimelineBundle\\Entity\\ActionComponentManger')->end()
                                ->scalarNode('component')->defaultValue('Bruery\\TimelineBundle\\Entity\\ComponentManager')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('timeline')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Bruery\\TimelineBundle\\Admin\\TimelineAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('BrueryTimelineBundle:TimelineAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('SonataTimelineBundle')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('block')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('timeline')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Bruery\\TimelineBundle\\Block\\TimelineBlockService')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
