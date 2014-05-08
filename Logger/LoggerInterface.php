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
 * interface LoggerInterface
 *
 * @package Smalot\MagentoBundle\Logger
 */
interface LoggerInterface
{
    /**
     * @return int
     */
    public function start();

    /**
     * @param int    $logNumber
     * @param string $connection
     * @param string $type
     * @param string $output
     * @param bool   $onError
     * @return void
     */
    public function stop($logNumber, $connection, $type, $output = '', $onError = false);

    /**
     * @return array
     */
    public function getCalls();
}