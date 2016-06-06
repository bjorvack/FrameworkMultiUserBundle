<?php

namespace SumoCoders\FrameworkMultiUserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sumo_coders_framework_multi_user');

        $rootNode
            ->children()
                ->arrayNode('redirect_routes')
                    ->example(array(
                        'Acme\DemoBundle\Entity\User1' => 'dashboard_route',
                    ))
                    ->useAttributeAsKey('class')
                    ->prototype('array')
                        ->beforeNormalization()->ifString()->then(function ($v) { return array('route' => $v); })->end()
                        ->children()
                            ->scalarNode('route')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
