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

use Smalot\Magento\ActionInterface;
use Smalot\Magento\MultiCallQueueInterface;
use Smalot\Magento\RemoteAdapter as BaseRemoteAdapter;
use Smalot\MagentoBundle\Event\MultiCallTransportEvent;
use Smalot\MagentoBundle\Event\SecurityEvent;
use Smalot\MagentoBundle\Event\SingleCallTransportEvent;
use Smalot\MagentoBundle\Logger\LoggerInterface;
use Smalot\MagentoBundle\MagentoException;
use Smalot\MagentoBundle\MagentoEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class RemoteAdapter
 *
 * @package Smalot\MagentoBundle\Adapter
 */
class RemoteAdapter extends BaseRemoteAdapter
{
    /**
     * @var string
     */
    protected $connection;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var \Smalot\MagentoBundle\Logger\LoggerInterface
     */
    protected $logger;

    /**
     * @param string $connection
     * @param string $path
     * @param string $apiUser
     * @param string $apiKey
     * @param array  $options
     * @param bool   $autoLogin
     */
    public function __construct($connection, $path, $apiUser, $apiKey, $options = array(), $autoLogin = true)
    {
        $this->connection = $connection;

        parent::__construct($path, $apiUser, $apiKey, $options, $autoLogin);
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     *
     * @return $this
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param string $apiUser
     * @param string $apiKey
     *
     * @return bool
     * @throws \Exception
     */
    public function login($apiUser = null, $apiKey = null)
    {
        $apiUser = (null === $apiUser ? $this->apiUser : $apiUser);
        $apiKey  = (null === $apiKey ? $this->apiKey : $apiKey);

        $event = new SecurityEvent($this, $apiUser, $apiKey);
        $this->dispatcher->dispatch(MagentoEvents::PRE_LOGIN, $event);

        // Retrieve ApiUser and ApiKey from SecurityEvent to allow override mechanism.
        $apiUser = $event->getApiUser();
        $apiKey  = $event->getApiKey();

        if (null !== $this->logger) {
            $logId           = $this->logger->start();
            $this->sessionId = $this->soapClient->login($apiUser, $apiKey);
            $this->logger->stop($logId, $this->connection, 'login', 'session: ' . $this->sessionId);
        } else {
            $this->sessionId = $this->soapClient->login($apiUser, $apiKey);
        }

        $event = new SecurityEvent($this, $apiUser, $apiKey, $this->sessionId);
        $this->dispatcher->dispatch(MagentoEvents::POST_LOGIN, $event);

        if ($this->sessionId) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function logout()
    {
        $event = new SecurityEvent($this, null, null, $this->sessionId);
        $this->dispatcher->dispatch(MagentoEvents::PRE_LOGOUT, $event);

        if (null !== $this->sessionId) {
            if (null !== $this->logger) {
                $logId = $this->logger->start();
                $this->soapClient->endSession($this->sessionId);
                $this->logger->stop($logId, $this->connection, 'logout', 'session: ' . $this->sessionId);
            } else {
                $this->soapClient->endSession($this->sessionId);
            }

            $event = new SecurityEvent($this, null, null, $this->sessionId);
            $this->dispatcher->dispatch(MagentoEvents::POST_LOGOUT, $event);

            $this->sessionId = null;

            return true;
        }

        return false;
    }

    /**
     * @param ActionInterface $action
     * @param bool            $throwsException
     *
     * @return array|null
     * @throws MagentoException
     */
    public function call(ActionInterface $action, $throwsException = true)
    {
        try {
            if (is_null($this->sessionId) && $this->autoLogin) {
                $this->login();
            }

            if (is_null($this->sessionId)) {
                throw new MagentoException('Not connected.');
            }

            $event = new SingleCallTransportEvent($this, $action);
            $this->dispatcher->dispatch(MagentoEvents::PRE_SINGLE_CALL, $event);
            $action = $event->getAction();

            if (null !== $this->logger) {
                $logId  = $this->logger->start();
                $result = $this->soapClient->call($this->sessionId, $action->getMethod(), $action->getArguments());
                $this->logger->stop($logId, $this->connection, 'call', 'action: ' . $action->getMethod());
            } else {
                $result = $this->soapClient->call($this->sessionId, $action->getMethod(), $action->getArguments());
            }

            $event = new SingleCallTransportEvent($this, $action, $result);
            $this->dispatcher->dispatch(MagentoEvents::POST_SINGLE_CALL, $event);
            $result = $event->getResult();

            return $result;

        } catch (MagentoException $e) {
            if ($throwsException) {
                throw $e;
            }

            return null;
        }
    }

    /**
     * @param MultiCallQueueInterface $queue
     * @param bool                    $throwsException
     *
     * @return array
     * @throws MagentoException
     */
    public function multiCall(MultiCallQueueInterface $queue, $throwsException = false)
    {
        try {
            $this->checkSecurity();

            $event = new MultiCallTransportEvent($this, $queue);
            $this->dispatcher->dispatch(MagentoEvents::PRE_MULTI_CALL, $event);
            $queue = $event->getQueue();

            $actions = $this->getActions($queue);

            if (null !== $this->logger) {
                $logId   = $this->logger->start();
                $results = $this->soapClient->multiCall($this->sessionId, $actions);
                $this->logger->stop($logId, $this->connection, 'multicall', 'queue: ' . count($actions) . ' action(s)');
            } else {
                $results = $this->soapClient->multiCall($this->sessionId, $actions);
            }

            $event = new MultiCallTransportEvent($this, $queue, $results);
            $this->dispatcher->dispatch(MagentoEvents::POST_MULTI_CALL, $event);
            $queue   = $event->getQueue();
            $results = $event->getResults();

            $this->handleCallbacks($queue, $results);

            return $results;

        } catch (MagentoException $e) {
            return array();
        }
    }
}
