<?php

namespace apps\admin\plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Dispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;

/**
 * NotFoundPlugin
 *
 * Handles not-found controller/actions & 5XX errors
 */
class NotFoundPlugin extends Plugin
{

    /**
     * This action is executed before execute any action in the application
     * @param Event $event
     * @param MvcDispatcher $dispatcher
     * @param \Exception $exception
     * @return bool
     */
    public function beforeException(Event $event, MvcDispatcher $dispatcher, \Exception $exception)
    {
        if ($exception instanceof DispatcherException) {
            switch ($exception->getCode()) {
                case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND :
                case Dispatcher::EXCEPTION_ACTION_NOT_FOUND :
                    $dispatcher->forward([
                        'controller' => 'errors',
                        'action'     => 'show404'
                    ]);
                    return false;
            }
        }
        $dispatcher->forward([
            'controller' => 'errors',
            'action'     => 'show500',
            'params'     => [
                'error_messge' => $exception->getMessage(),
                'error_code'   => $exception->getCode(),
                'error_file'   => $exception->getFile(),
                'error_trace'  => $exception->getTrace()
            ]
        ]);

        if ($this->di->getConfig()->debug->tools) {
            \PhalconDebug::debug(new \Exception ($exception->getMessage()));
            \PhalconDebug::debug(new \Exception ($exception->getTraceAsString()));

        }
        return false;
    }
}