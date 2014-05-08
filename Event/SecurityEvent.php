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

use Smalot\Magento\RemoteAdapterInterface;

/**
 * Class SecurityEvent
 *
 * @package Smalot\MagentoBundle\Event
 */
class SecurityEvent extends AbstractEvent
{
    /**
     * @var string
     */
    protected $apiUser;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @param RemoteAdapterInterface $remoteAdapter
     * @param string                 $apiUser
     * @param string                 $apiKey
     * @param string                 $sessionId
     */
    public function __construct(RemoteAdapterInterface $remoteAdapter, $apiUser, $apiKey, $sessionId = null)
    {
        $this->remoteAdapter = $remoteAdapter;
        $this->apiUser       = $apiUser;
        $this->apiKey        = $apiKey;
        $this->sessionId     = $sessionId;
    }

    /**
     * @param string $apiUser
     */
    public function setApiUser($apiUser)
    {
        $this->apiUser = $apiUser;
    }

    /**
     * @return string
     */
    public function getApiUser()
    {
        return $this->apiUser;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }
}
