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

use Smalot\Magento\ActionInterface;
use Smalot\Magento\RemoteAdapterInterface;

/**
 * Class SingleCallTransportEvent
 *
 * @package Smalot\MagentoBundle\Event
 */
class SingleCallTransportEvent extends AbstractEvent
{
    /**
     * @var ActionInterface
     */
    protected $action;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @param RemoteAdapterInterface $remoteAdapter
     * @param ActionInterface        $action
     * @param mixed                  $result
     */
    public function __construct(RemoteAdapterInterface $remoteAdapter, ActionInterface $action, $result = null)
    {
        $this->remoteAdapter = $remoteAdapter;
        $this->action        = $action;
        $this->result        = $result;
    }

    /**
     * @param ActionInterface $action
     */
    public function setAction(ActionInterface $action)
    {
        $this->action = $action;
    }

    /**
     * @return ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
}
