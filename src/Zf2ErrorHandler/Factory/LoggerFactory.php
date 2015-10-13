<?php

namespace Zf2ErrorHandler\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * LoggerFactory
 * @author Venugopal Thotakura <venu@riktamtech.com>
 */
class LoggerFactory implements FactoryInterface
{

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return Logger
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $filename = date('Y-m-d') . '_error' . '.log';
        $logger = new \Zend\Log\Logger();
        $writer = new \Zend\Log\Writer\Stream('./data/logs/' . $filename);
        $logger->addWriter($writer);

        \Zf2ErrorHandler\Log\ExceptionLogger::setLoggerExceptionHandler($logger);

        return $logger;
    }

}
