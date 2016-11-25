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

namespace Smalot\MagentoBundle\Adapter;

use Smalot\Magento\RemoteAdapterInterface;
use Smalot\MagentoBundle\Logger\LoggerInterface;
use Smalot\MagentoBundle\MagentoException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class FactoryAdapter
 *
 * @package Smalot\MagentoBundle\Adapter
 */
class FactoryAdapter implements ContainerAwareInterface
{
    /**
     * @var array
     */
    protected $instances = array();

    /**
     * @var string
     */
    protected $defaultClass;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param string                   $defaultClass
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     */
    public function __construct(
      $defaultClass = null,
      EventDispatcherInterface $dispatcher = null,
      LoggerInterface $logger = null
    ) {
        $this->defaultClass = $defaultClass;
        $this->dispatcher   = $dispatcher;
        $this->logger       = $logger;
    }

    /**
     * Sets the Container associated with this Controller.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        // Load settings.
        $this->settings = $this->container->getParameter('magento');
    }

    /**
     * @param string $name
     * @param array  $options
     * @param bool   $autoLogin
     *
     * @return RemoteAdapterInterface
     * @throws MagentoException
     */
    public function getManager($name = null, $options = array(), $autoLogin = true)
    {
        // Get default connection if necessary.
        $name = $this->getConnectionName($name);

        // Check availability of connection name.
        if (empty($name) || !isset($this->settings['connections'][$name])) {
            throw new MagentoException('Missing or not found connector name.');
        }

        // Create new instance.
        if (!isset($this->instances[$name])) {
            $settings = $this->settings['connections'][$name];

            $settings += array(
              'logging'    => false,
              'logger'     => null,
              'dispatcher' => null,
            );

            if (isset($settings['class']) && !empty($settings['class'])) {
                $class = $settings['class'];
            } else {
                $class = $this->defaultClass;
            }

            $this->instances[$name] = $this->createInstance($name, $class, $settings, $options, $autoLogin);
        }

        return $this->instances[$name];
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getConnectionName($name)
    {
        // Use default connection.
        if (null === $name) {
            if (isset($this->settings['default_connection'])) {
                $name = $this->settings['default_connection'];
            } else {
                if (null === $this->settings['default_connection'] && count($this->settings['connections']) == 1) {
                    /** @var array $connections */
                    $connections = $this->settings['connections'];
                    $name        = key($connections);
                }
            }
        }

        return $name;
    }

    /**
     * @param string $name
     * @param string $class
     * @param array  $settings
     * @param array  $options
     * @param bool   $autoLogin
     *
     * @return RemoteAdapter
     */
    protected function createInstance($name, $class, $settings, $options = array(), $autoLogin = true)
    {
        /** @var RemoteAdapter $instance */
        $instance = new $class($name, $settings['url'], $settings['api_user'], $settings['api_key'], $options, $autoLogin);
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = (null !== $settings['dispatcher'] ? $settings['dispatcher'] : $this->dispatcher);
        $instance->setDispatcher($dispatcher);
        /** @var LoggerInterface $logger */
        $logger = (null !== $settings['logger'] && $settings['logging'] ? $settings['logger'] : $this->logger);
        $instance->setLogger($logger);

        return $instance;
    }
}
