<?php

/**
 * @project Magento Bridge for Symfony 2.
 *
 * @author  Sébastien MALOT <sebastien@malot.fr>
 * @license MIT
 * @url     <https://github.com/smalot/magento-bundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Smalot\MagentoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Smalot\MagentoBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('magento');

        $rootNode
          ->children()
            ->arrayNode('connections')->info('List all available connections')
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('default')
                ->prototype('array')
                  ->children()
                      ->scalarNode('url')->isRequired()->cannotBeEmpty()->example('http://domain.tld/magento/')->end()
                      ->scalarNode('api_user')->isRequired()->cannotBeEmpty()->example('username')->end()
                      ->scalarNode('api_key')->isRequired()->cannotBeEmpty()->example('0123456789AZ')->end()
                      ->booleanNode('logging')->defaultValue(false)->info('Enable logging system')->example('%kernel.debug%')->end()
                      ->scalarNode('logger')->defaultValue(null)->info('Refers to the logger service')->end()
                      ->scalarNode('dispatcher')->defaultValue(null)->info('Refers to the dispatcher service')->end()
                  ->end()
                ->end()
            ->end()
            ->scalarNode('default_connection')->defaultValue('default')->info('Refers to the default connection in the connection pool')->example('default')
            ->end()
          ->end()
        ;

        return $treeBuilder;
    }
}
