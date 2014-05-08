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

namespace Smalot\MagentoBundle\Logger;

/**
 * Class MagentoLogger
 *
 * @package Smalot\MagentoBundle\Logger
 */
class MagentoLogger implements LoggerInterface
{
    /**
     * @var array
     */
    protected $calls = array();

    /**
     * @return int
     */
    public function start()
    {
        $currentCall               = count($this->calls);
        $this->calls[$currentCall] = array('time' => microtime(true));

        return $currentCall;
    }

    /**
     * @param int    $logNumber
     * @param string $connection
     * @param string $type
     * @param string $output
     * @param bool   $onError
     */
    public function stop($logNumber, $connection, $type, $output = '', $onError = false)
    {
        $time = microtime(true) - $this->calls[$logNumber]['time'];

        $this->calls[$logNumber] = array(
          'connection' => $connection,
          'type'       => $type,
          'error'      => $onError,
          'output'     => $output,
          'time'       => $time,
        );
    }

    /**
     * @return array
     */
    public function getCalls()
    {
        return $this->calls;
    }
}