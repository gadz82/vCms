<?php

namespace apps\cron\plugins;
use apps\cron\exception\CronException;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Dispatcher;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;

/**
 * NotFoundPlugin
 *
 * Handles not-found controller/actions
 */
class ExceptionHandlerPlugin extends Plugin {
	
	/**
	 * This action is executed before execute any action in the application
	 *
	 * @param Event $event        	
	 * @param Dispatcher $dispatcher
	 */
	public function beforeException(Event $event, MvcDispatcher $dispatcher, \Exception $exception) {

	    if($exception instanceof CronException){
	        $log = new \CronLog();
            $log->codice = $exception->getCode();
            $log->text = $exception->getMessage();
            if($exception->getEntity() !== null){
                $log->entity = $exception->getEntity();
            }
            $log->data_creazione = (new \DateTime())->format('Y-m-d H:i:s');
            $log->save();

            $this->response->setJsonContent([
                'codice' => $exception->getCode(),
                'messaggio' => $exception->getMessage()
            ]);
            $this->response->send();
            exit();
        } else {

            $code = in_array($exception->getCode(), ['404', '403', '500', '505', '301', '300']) ? $exception->getCode() : 404;

            $this->response->setStatusCode($code);
            $this->response->setJsonContent([
                    'message' => $code == '404' ? "Endpoint not found" : $exception->getMessage(),
                    'code' => $code
                ]
            );
            $this->response->send();
            exit();
        }
	}
}
