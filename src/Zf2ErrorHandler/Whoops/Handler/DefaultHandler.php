<?php

namespace Zf2ErrorHandler\Whoops\Handler;

use Whoops\Exception\Formatter;
use Whoops\Handler\Handler;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

/**
 * DefaultHandler
 * @author Venugopal Thotakura <venu@riktamtech.com>
 */
class DefaultHandler extends Handler
{

    public function setRenderer(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return int
     */
    public function handle()
    {
        $response = Formatter::formatExceptionAsDataArray($this->getInspector(), 1);

        $viewModel = new ViewModel();
        $viewModel->setTemplate('error/500');
        $viewModel->setVariables($response);
        echo $this->renderer->render($viewModel);

        return \Whoops\Handler\Handler::QUIT;
    }

}
