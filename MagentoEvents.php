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

namespace Smalot\MagentoBundle;

/**
 * Class MagentoEvents
 *
 * @package Smalot\MagentoBundle
 */
final class MagentoEvents
{
    /**
     * Security events.
     */
    const PRE_LOGIN = 'magento_api.security.login.pre';

    const POST_LOGIN = 'magento_api.security.login.post';

    const PRE_LOGOUT = 'magento_api.security.logout.pre';

    const POST_LOGOUT = 'magento_api.security.logout.post';

    /**
     * Transport events.
     */
    const PRE_SINGLE_CALL = 'magento_api.transport.single_call.pre';

    const POST_SINGLE_CALL = 'magento_api.transport.single_call.post';

    const PRE_MULTI_CALL = 'magento_api.transport.multi_call.pre';

    const POST_MULTI_CALL = 'magento_api.transport.multi_call.post';
}
