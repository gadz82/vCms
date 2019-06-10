<?php

namespace apps\admin\plugins;

use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Event;

class CheckRoutePlugin extends Plugin {
	
	/**
	 * Funzione eseguita prima della route
	 *
	 * @param Event $event        	
	 * @param Dispatcher $dispatcher        	
	 */
	public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher) {
		$error = false;
		$r = new \ReflectionMethod ( $dispatcher->getHandlerClass (), $dispatcher->getActiveMethod () );
		$method_params = $r->getParameters ();
		$url_pars = $dispatcher->getParams ();
		
		$id = 0;
		foreach ( $method_params as $param ) {
			if (! $param->isOptional () && ! isset ( $url_pars [$id ++] )) {
				$error = true;
				break;
			}
		}
		
		if ($error) {
			$this->flashSession->error ( 'Attenzione! Hai effettuato una richiesta non valida!' );
			return $this->di->getResponse ()->redirect ( $dispatcher->getControllerName () . '/index' );
		}
	}
}