<?php

namespace apps\cron\plugins;

use Phalcon\Annotations\Exception;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Event;

class CheckRoutePlugin extends Plugin {

    /**
     * @param Event $event
     * @param Dispatcher $dispatcher
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher) {

        try{
            /**
             * @TODO configuration con container ip autorizzati
             */
            if(!in_array($_SERVER['REMOTE_ADDR'], $this->config->application->allowedIp->toArray())) {
                return $this->response->redirect('/', false, 301);
                throw new Exception('Unallowed ip');
            }

        } catch(\Exception $exception){
            return $this->response->redirect('/', false, 301);
            exit();
        }
    }

}