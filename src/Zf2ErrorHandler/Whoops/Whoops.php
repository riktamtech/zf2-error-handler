<?php

namespace Zf2ErrorHandler\Whoops;

use Whoops\Handler\Handler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Zend\Log\Logger;
use Zf2ErrorHandler\Whoops\Handler\DefaultHandler;

/**
 * Whoops
 * @author Venugopal Thotakura <venu@riktamtech.com>
 */
class Whoops
{

    /**
     * @var Run
     */
    private $run;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Handler
     */
    private $jsonHandler;

    /**
     * @var boolean
     */
    private $displayExceptionsinHtml = false;

    /**
     * @var array
     */
    private $jsonHandlerConfig;
    
    /**
     * @var \Zend\View\Renderer\PhpRenderer
     */
    public $renderer;

    public function __construct()
    {
        $this->run = new Run();
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Whoops\Handler\HandlerInterface $handler
     */
    public function setJsonHandler(\Whoops\Handler\HandlerInterface $handler)
    {
        $this->jsonHandler = $handler;
    }

    public function setRenderer(\Zend\View\Renderer\PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return \Whoops\Run
     */
    public function getRun()
    {
        return $this->run;
    }

    /**
     * @param array $config
     */
    public function initFromConfig(array $config)
    {
        if (isset($config['display_html_exceptions'])) {
            $this->displayExceptionsinHtml = (bool) $config['display_html_exceptions'];
        }

        if (isset($config['json_response_handler'])) {
            $this->jsonHandlerConfig = $config['json_response_handler'];
        }
    }

    public function register()
    {
        $this->run->register();

        $this->setupDefaultHandler();

        if ($this->displayExceptionsinHtml) {
            $this->setupPrettyPageHandler();
        }

        if ($this->jsonHandlerConfig['display']) {
            $this->setupJsonResponseHandler();
        }

        if ($this->getLogger()) {
            $this->setupLoggerHandler();
        }
    }

    protected function setupJsonResponseHandler()
    {
        if (!empty($this->jsonHandlerConfig['show_trace'])) {
            $this->jsonHandler->addTraceToOutput(true);
        }
        if (!empty($this->jsonHandlerConfig['ajax_only'])) {
            $this->jsonHandler->onlyForAjaxRequests(true);
        }
        if (!empty($this->jsonHandlerConfig['context_based_only']) && method_exists($this->jsonHandler, 'onlyForJsonRequests')) {
            $this->jsonHandler->onlyForJsonRequests(true);
        }

        $this->run->pushHandler($this->jsonHandler);
    }

    protected function setupDefaultHandler()
    {
        $handler = new DefaultHandler();
        $handler->setRenderer($this->renderer);
        $this->run->pushHandler($handler);
    }

    protected function setupPrettyPageHandler()
    {
        $handler = new PrettyPageHandler();
        $this->run->pushHandler($handler);
    }

    protected function setupLoggerHandler()
    {
        $whoops = $this;
        $closure = function ($exception, $inspector, $run) use ($whoops) {
            $excelptionLogger = \Zf2ErrorHandler\Log\ExceptionLogger::$loggerExceptionHandler;
            $excelptionLogger($exception);
            return Handler::DONE;
        };
        $this->run->pushHandler($closure);
    }

}
