<?php

namespace apps\api\plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Dispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;

/**
 * NotFoundPlugin
 *
 * Handles not-found controller/actions
 */
class ExceptionHandlerPlugin extends Plugin
{

    /**
     * This action is executed before execute any action in the application
     *
     * @param Event $event
     * @param Dispatcher $dispatcher
     */
    public function beforeException(Event $event, MvcDispatcher $dispatcher, \Exception $exception)
    {
        print_r($exception->getMessage());
        exit();
        $code = in_array($exception->getCode(), ['404', '403', '500', '505', '301', '300']) ? $exception->getCode() : 404;

        $this->response->setStatusCode($code);
        $this->response->setJsonContent([
                'message' => $code == '404' ? "Endpoint not found" : $exception->getMessage(),
                'code'    => $code
            ]
        );

        $this->response->send();
        exit();
    }
}
