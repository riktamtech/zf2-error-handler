<?php

namespace Zf2ErrorHandler\Log;

use Zend\Log\Logger as Logger;

/**
 * ExceptionLogger
 * @author Venugopal Thotakura <venu@riktamtech.com>
 */
class ExceptionLogger
{

    /**
     * @var Closure
     */
    public static $loggerExceptionHandler;

    /**
     * @param Logger $logger
     */
    public static function setLoggerExceptionHandler(Logger $logger)
    {
        if (self::$loggerExceptionHandler) {
            return;
        }

        $previousHandler = set_exception_handler(function () {});
        Logger::registerExceptionHandler($logger);
        self::$loggerExceptionHandler = set_exception_handler(function () {});
        Logger::unregisterExceptionHandler();
        set_exception_handler($previousHandler);
    }

}
