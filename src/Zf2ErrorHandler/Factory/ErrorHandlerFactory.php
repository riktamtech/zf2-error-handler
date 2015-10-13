<?php

namespace Zf2ErrorHandler\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ErrorHandlerFactory
 * @author Venugopal Thotakura <venu@riktamtech.com>
 */
class ErrorHandlerFactory implements FactoryInterface
{

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return ErrorHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        $config = $serviceLocator->get('Config');
        $zf2ErrorHandlerConfig = $config['zf2-error-handler'];

        $whoops = $serviceLocator->get('Zf2ErrorHandler\Whoops');
        $exceptionHandler = new \Zf2ErrorHandler\Handler\ErrorHandler($whoops, $config['zf2-error-handler']['ignore_exceptions']);

        if (isset($zf2ErrorHandlerConfig['logger']) && $zf2ErrorHandlerConfig['logger']) {
            $exceptionHandler->setLogger($serviceLocator->get($zf2ErrorHandlerConfig['logger']));
        }

        if (isset($zf2ErrorHandlerConfig['php_settings']) && $zf2ErrorHandlerConfig['php_settings']) {
            $exceptionHandler->setPhpSettings($zf2ErrorHandlerConfig['php_settings']);
        }

        return $exceptionHandler;
    }

}
