<?php

namespace Zf2ErrorHandler\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * WhoopsFactory
 * @author Venugopal Thotakura <venu@riktamtech.com>
 */
class WhoopsFactory implements FactoryInterface
{

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return Whoops
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        $config = $serviceLocator->get('Config');
        $zf2ErrorHandlerConfig = $config['zf2-error-handler'];

        $whoops = new \Zf2ErrorHandler\Whoops\Whoops();
        $whoops->initFromConfig($zf2ErrorHandlerConfig);
        $whoops->setRenderer($serviceLocator->get('viewmanager')->getRenderer());

        $jsonHandler = !empty($zf2ErrorHandlerConfig['json_response_handler']['handler_class']) ? $zf2ErrorHandlerConfig['json_response_handler']['handler_class'] : 'Zf2ErrorHandler\Whoops\JsonResponseHandler';
        $whoops->setJsonHandler($serviceLocator->get($jsonHandler));

        if (isset($zf2ErrorHandlerConfig['logger']) && $zf2ErrorHandlerConfig['logger']) {
            $whoops->setLogger($serviceLocator->get($zf2ErrorHandlerConfig['logger']));
        }

        $whoops->register();

        return $whoops;
    }

}
