<?php

namespace UCI\Boson\ExcepcionesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Esta es la clase que valida y mezcla la configuración de los ficheros encontrados en app/config
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
        $rootNode = $treeBuilder->root('excepciones');
        $rootNode->children()
            ->scalarNode('email_admin_contact')->isRequired()->end()
            ->end();
        return $treeBuilder;
    }
}
