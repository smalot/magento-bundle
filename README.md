MagentoBundle
=============

This project is a bridge between Symfony 2 and Magento-Client API which allow to call easily the Magento Soap v1 API.

Allows :
- wrappers for each call
- dependencies injections
- event listeners
- debug toolbar integration
- and ... code completion.

Requirements
============

* Symfony >= 2.1
* PHP >= 5.3
* smalot/magento-client

Installation
============

Add the following lines to your composer.json:

```json
{
    "require": {
        "smalot/magento-bundle": "*"
    }
}
```

And run `php composer.phar update smalot/magento-bundle`

Then, register the bundle in your kernel:

```php
# app/AppKernel.php

# ...

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            # ...
            new Smalot\MagentoBundle(),
        );

        return $bundles;
    }
}
```

Configuration
-------------

```yaml
# app/config/config.yml

# Default configuration for extension with alias: "magento"
magento:
    # Refers to the default connection in the connection pool
    default_connection:   default # Example: default

    # List all available connections
    connections:

        # Prototype
        default:
            url:                  http://domain.tld/magento/
            api_user:             username
            api_key:              0123456789AZ

            # Enable logging system
            logging:              %kernel.debug%

            # Refers to the logger service
            logger:               ~

            # Refers to the dispatcher service
            dispatcher:           ~
```


Usage
-----

Service(s) provided:
- magento

Events thrown in security context:
- \Smalot\MagentoBundle\MagentoEvents::PRE_LOGIN
- \Smalot\MagentoBundle\MagentoEvents::POST_LOGIN
- \Smalot\MagentoBundle\MagentoEvents::PRE_LOGOUT
- \Smalot\MagentoBundle\MagentoEvents::POST_LOGOUT

Events thrown in transport context:
- \Smalot\MagentoBundle\MagentoEvents::PRE_SINGLE_CALL
- \Smalot\MagentoBundle\MagentoEvents::POST_SINGLE_CALL
- \Smalot\MagentoBundle\MagentoEvents::PRE_MULTI_CALL
- \Smalot\MagentoBundle\MagentoEvents::POST_MULTI_CALL

```php

class MagentoController extends Controller
{
    /**
     * @Route("/", name="magento_index")
     */
    public function indexAction(Request $request)
    {
        $magento = $this->get('magento')->getManager();

        if ($magento->ping()) {
            // Call any module's class
            $categoryManager = new \Smalot\Magento\Catalog\Category($magento);
            $tree            = $categoryManager->getTree()->execute();
        } else {
            $tree = array();
        }

        $magento->logout();

        return new Response('<html><body><pre>' . var_export($tree, true) . '</pre></body></html>');
    }
}

```