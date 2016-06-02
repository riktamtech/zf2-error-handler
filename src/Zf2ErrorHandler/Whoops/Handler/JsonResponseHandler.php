<?php

namespace Zf2ErrorHandler\Whoops\Handler;

use Whoops\Exception\Formatter;
use Whoops\Handler\JsonResponseHandler as BaseJsonResponseHandler;

/**
 * JsonResponseHandler
 * @author Venugopal Thotakura <venu@riktamtech.com>
 */
class JsonResponseHandler extends BaseJsonResponseHandler
{

    /**
     * @var bool
     */
    private $onlyForJsonRequests = false;

    /**
     * Check, if possible, that this execution was triggered by an AJAX request.
     *
     * @return bool
     */
    private function isAjaxRequest()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * Check Accept type is json.
     *
     * @return bool
     */
    private function isJsonRequest()
    {
        return (!empty($_SERVER['HTTP_ACCEPT']) && strstr($_SERVER['HTTP_ACCEPT'], 'application/json'));
    }

    /**
     * @param  bool|null $onlyForJsonRequests
     * @return null|bool
     */
    public function onlyForJsonRequests($onlyForJsonRequests = null)
    {
        if (func_num_args() == 0) {
            return $this->onlyForJsonRequests;
        }

        $this->onlyForJsonRequests = (bool) $onlyForJsonRequests;
    }

    /**
     * @return int
     */
    public function handle()
    {
        if ($this->onlyForAjaxRequests() && !$this->isAjaxRequest()) {
            return \Whoops\Handler\Handler::DONE;
        }

        if ($this->onlyForJsonRequests() && !$this->isJsonRequest()) {
            return \Whoops\Handler\Handler::DONE;
        }

        $response = array(
            'error' => Formatter::formatExceptionAsDataArray(
                    $this->getInspector(), $this->addTraceToOutput()
            ),
        );

        unset($response['error']['file']);
        unset($response['error']['line']);
        $response['error']['code'] = $this->getException()->getCode();

        if (\Whoops\Util\Misc::canSendHeaders()) {
            http_response_code($response['error']['code']);
            header('Content-Type: application/json');
        }

        echo json_encode($response, defined('JSON_PARTIAL_OUTPUT_ON_ERROR') ? JSON_PARTIAL_OUTPUT_ON_ERROR : 0);

        return \Whoops\Handler\Handler::QUIT;
    }

}
