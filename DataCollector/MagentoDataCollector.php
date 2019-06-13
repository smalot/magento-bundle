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

namespace Smalot\MagentoBundle\DataCollector;

use Smalot\MagentoBundle\Logger\LoggerInterface;
use Smalot\MagentoBundle\Logger\MagentoLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Class MagentoDataCollector
 *
 * @package Smalot\MagentoBundle\Collector
 */
class MagentoDataCollector extends DataCollector
{
    /**
     * @var MagentoLogger
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Request    $request
     * @param Response   $response
     * @param \Exception $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if ($this->logger) {
            $this->data = array('magento' => $this->logger->getCalls());
        } else {
            $this->data = array('magento' => array());
        }
    }

    /**
     * Return total duration in seconds.
     *
     * @return float
     */
    public function getTime()
    {
        $time = 0.0;

        foreach ($this->data['magento'] as $log) {
            $time += $log['time'];
        }

        return $time;
    }

    /**
     * @return array
     */
    public function getCalls()
    {
        $calls = array();

        foreach ($this->data['magento'] as $call) {
            $calls[$call['connection']][] = $call;
        }

        return $calls;
    }

    /**
     * @return int
     */
    public function getCallCount()
    {
        return count($this->data['magento']);
    }

    /**
     * @return int
     */
    public function getInvalidEntityCount()
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'magento';
    }
    
    /**
     * 
     */
    public function reset() {
        $this->data = array();
    }
}
