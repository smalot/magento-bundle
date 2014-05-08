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
 * Class AbstractEvent
 *
 * @package Smalot\MagentoBundle\Event
 */
abstract class AbstractEvent extends Event
{
    /**
     * @var \Smalot\Magento\RemoteAdapterInterface
     */
    protected $remoteAdapter;

    /**
     * @return \Smalot\Magento\RemoteAdapterInterface
     */
    public function getRemoteAdapter()
    {
        return $this->remoteAdapter;
    }
}
