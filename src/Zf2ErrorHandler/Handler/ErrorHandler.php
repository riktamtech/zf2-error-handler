<?php

namespace Zf2ErrorHandler\Handler;

use Exception;
use Zend\Log\Logger;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ResponseInterface as Response;
use Zf2ErrorHandler\Whoops\Whoops;

/**
 * ErrorHandler
 * @author Venugopal Thotakura <venu@riktamtech.com>
 */
class ErrorHandler
{

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Whoops
     */
    private $whoops;

    /**
     * @var array
     */
    private $ignoredExceptions = array();

    public function __construct($whoops, $ignoredExceptions)
    {
        $this->whoops = $whoops;
        $this->ignoredExceptions = $ignoredExceptions;
    }

    /**
     * @return \Zf2ErrorHandler\Whoops\Whoops
     */
    public function getWhoops()
    {
        return $this->whoops;
    }

    /**
     * @return \Zend\Log\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param \Zend\Log\Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array $settings
     */
    public function setPhpSettings(array $settings)
    {
        if (isset($settings)) {
            foreach ($settings as $key => $value) {
                ini_set($key, $value);
            }
        }
    }

    /**
     * @param MvcEvent $e
     */
    public function exceptionHandler(MvcEvent $e)
    {
        if (!$this->canHandlerError($e)) {
            return;
        }

        switch ($e->getError()) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
                $this->handleControllerError($e);
                return;

            case Application::ERROR_ROUTER_NO_MATCH:
                $this->handleRouteNoMatchError($e);
                return;

            case Application::ERROR_EXCEPTION:
            default:
                $this->handleException($e);
                return;
        }
    }

    /**
     * @param MvcEvent $e
     * @return bool
     */
    protected function canHandlerError(MvcEvent $e)
    {
        return $e->getError() && !$e->getResult() instanceof Response;
    }

    /**
     * @param MvcEvent $e
     */
    protected function handleControllerError(MvcEvent $e)
    {
        if (!$this->exceptionHandlerExists()) {
            return;
        }

        $res = $e->getResult()->getVariables();
        $message = $res->controller . ' (' . $res->controller_class . ')';
        $this->writeExceptionToLogger(new \Zend\Mvc\Exception\InvalidControllerException($message));
    }

    /**
     * @param Exception $exception
     */
    public function writeExceptionToLogger(Exception $exception)
    {
        $excelptionLogger = \Zf2ErrorHandler\Log\ExceptionLogger::$loggerExceptionHandler;
        $excelptionLogger($exception);
    }

    /**
     * @param MvcEvent $e
     */
    protected function handleRouteNoMatchError(MvcEvent $e)
    {
        if (!$this->exceptionHandlerExists()) {
            return;
        }

        $requestUri = $e->getRequest()->getRequestUri();
        $res = $e->getResult()->getVariables();
        $message = $requestUri . ' (' . $res->reason . ', ' . $res->message . ')';
        $this->writeExceptionToLogger(new \Zend\Mvc\Router\Exception\RuntimeException($message));
    }

    /**
     * @param MvcEvent $e
     */
    protected function handleException(MvcEvent $e)
    {
        if ($this->shouldIgnoreException($e->getParam('exception'))) {
            $this->writeExceptionToLogger($e->getParam('exception'));
            return;
        }

        $response = $e->getResponse();
        if (!$response || $response->getStatusCode() === 200) {
            header('HTTP/1.0 500 Internal Server Error', true, 500);
        }

        ob_clean();
        $this->getWhoops()->getRun()->handleException($e->getParam('exception'));
    }

    /**
     * @param Exception $e
     * @return bool
     */
    public function shouldIgnoreException(Exception $e)
    {
        return in_array(get_class($e), $this->ignoredExceptions);
    }

    /**
     * @param Exception $e
     * @return bool
     */
    public function exceptionHandlerExists()
    {
        return \Zf2ErrorHandler\Log\ExceptionLogger::$loggerExceptionHandler;
    }

}
