<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamorw <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\EntityAuditBundle\DependencyInjection;

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
        $node = $treeBuilder->root('bruery_entity_audit');
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
                ->arrayNode('settings')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('entity_managers')
                            ->isRequired()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('source')->defaultValue('doctrine.orm.default_entity_manager')->isRequired()->end()
                                ->scalarNode('audit')->defaultValue('doctrine.orm.audit_entity_manager')->isRequired()->end()
                            ->end()
                        ->end()
                        ->arrayNode('audit')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('manager')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('Bruery\\EntityAuditBundle\\Model\\AuditManager')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('reader')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('Bruery\\EntityAuditBundle\\Model\\AuditReader')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('listener')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('log_revisions')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('class')->defaultValue('Bruery\\EntityAuditBundle\\EventListener\\LogRevisionsListener')->end()
                                                ->scalarNode('connection')->defaultValue('default')->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('create_schema')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('class')->defaultValue('Bruery\\EntityAuditBundle\\EventListener\\CreateSchemaListener')->end()
                                                ->scalarNode('connection')->defaultValue('audit')->end()
                                            ->end()
                                        ->end()

                                    ->end()
                                ->end()
                                ->arrayNode('config')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('Bruery\\EntityAuditBundle\\Model\\AuditConfiguration')->end()
                                    ->end()
                                ->end()
                            ->end()  #--end audit children
                        ->end() #--end audit
                    ->end()
                ->end()
            ->end();
    }
}
