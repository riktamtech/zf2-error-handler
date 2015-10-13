<?php

namespace Zf2ErrorHandler;

use Zend\Console\Request as ConsoleRequest;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * Module
 * @author Venugopal Thotakura <venu@riktamtech.com>
 */
class Module
{

    /**
     * @param MvcEvent $e
     * @return array|void
     */
    public function onBootstrap(MvcEvent $e)
    {
        if (PHP_SAPI === 'cli') {
            return;
        }

        if ($e->getRequest() instanceof ConsoleRequest) {
            return;
        }

        /** @var ServiceManager $serviceManager */
        $serviceManager = $e->getTarget()->getServiceManager();
        /** @var ErrorHandler $handler */
        $handler = $serviceManager->get('Zf2ErrorHandler\ErrorHandler');

        /** @var EventManagerInterface $eventManager */
        $eventManager = $e->getTarget()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, array($handler, 'exceptionHandler'));
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($handler, 'exceptionHandler'));
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'Zf2ErrorHandler\Whoops\JsonResponseHandler' => 'Zf2ErrorHandler\Whoops\Handler\JsonResponseHandler',
                'Whoops\JsonResponseHandler' => 'Whoops\Handler\JsonResponseHandler',
            ),
            'factories' => array(
                'Zf2ErrorHandler\ErrorHandler' => 'Zf2ErrorHandler\Factory\ErrorHandlerFactory',
                'Zf2ErrorHandler\Whoops' => 'Zf2ErrorHandler\Factory\WhoopsFactory',
                'Zf2ErrorHandler\Logger' => 'Zf2ErrorHandler\Factory\LoggerFactory',
            )
        );
    }

}
