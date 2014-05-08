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

namespace Smalot\MagentoBundle\Event;

use Smalot\Magento\MultiCallQueueInterface;
use Smalot\Magento\RemoteAdapterInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class MultiCallTransportEvent
 *
 * @package Smalot\MagentoBundle\Event
 */
class MultiCallTransportEvent extends Event
{
    /**
     * @var \Smalot\Magento\RemoteAdapterInterface
     */
    protected $remoteAdapter;

    /**
     * @var MultiCallQueueInterface
     */
    protected $queue;

    /**
     * @var mixed
     */
    protected $results;

    /**
     * @param RemoteAdapterInterface  $remoteAdapter
     * @param MultiCallQueueInterface $queue
     * @param mixed                   $results
     */
    public function __construct(RemoteAdapterInterface $remoteAdapter, MultiCallQueueInterface $queue, $results = null)
    {
        $this->remoteAdapter = $remoteAdapter;
        $this->queue         = $queue;
        $this->results       = $results;
    }

    /**
     * @return \Smalot\Magento\RemoteAdapterInterface
     */
    public function getRemoteAdapter()
    {
        return $this->remoteAdapter;
    }

    /**
     * @param MultiCallQueueInterface $queue
     */
    public function setQueue(MultiCallQueueInterface $queue)
    {
        $this->queue = $queue;
    }

    /**
     * @return MultiCallQueueInterface
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param array $results
     */
    public function setResults($results)
    {
        $this->results = $results;
    }

    /**
     * @return mixed
     */
    public function getResults()
    {
        return $this->results;
    }
}
