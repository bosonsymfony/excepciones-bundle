<?php

namespace UCI\Boson\ExcepcionesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Esta clase valida la configuración definida en tu Resources/config/excepciones.[yml, xml]
 *
 * @author Daniel Arturo Casals Amat <dacasals@uci.cu>
 */
class ExcepcionesConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('excepciones')
            ->children()
                ->arrayNode('excepciones')
                ->isRequired()
                //->requiresAtLeastOneElement()
                ->disallowNewKeysInSubsequentConfigs()
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->children()
                        ->scalarNode('mensaje')->end()
                        ->scalarNode('descripcion')->end()
                        ->booleanNode('show_in_prod')->end()
                ->end()
                ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
