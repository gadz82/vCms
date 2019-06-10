<?php

namespace apps\api\plugins;

use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Event;

class CheckRoutePlugin extends Plugin {

    private static function removeUnicodeSequences($struct) {
        return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $struct);
    }

    /**
     * Funzione eseguita prima della route
     *
     * @param Event $event
     * @param Dispatcher $dispatcher
     */
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher) {

        $error = false;
        $url_pars = $dispatcher->getParams ();

        if(in_array($dispatcher->getControllerName(), ['entity', 'list']) && in_array($dispatcher->getActiveMethod(),['readAction', 'fetchAction'])){
            switch($dispatcher->getControllerName()){
                case 'entity':
                    $controllerName = \Phalcon\Text::camelize($url_pars['post_type_slug']).'EntityController';
                    $dispatchableController = $url_pars['post_type_slug'].'Entity';
                    $actionName = 'read';
                    $subClassOf = 'EntityController';
                    break;
                case 'list':
                    $controllerName = \Phalcon\Text::camelize($url_pars['post_type_slug']).'ListController';
                    $dispatchableController = $url_pars['post_type_slug'].'List';
                    $actionName = 'list';
                    $subClassOf = 'ListController';
                    break;
            }

            if( is_file($this->config->application->controllersDir . $controllerName . '.php') && class_exists($controllerName) ){
                $r = new \ReflectionClass( $controllerName );


                if(is_subclass_of($controllerName, $subClassOf) && $r->hasMethod($actionName.'Action')){
                    if(isset($url_pars['post_slug'])){
                        $childMethod = \Phalcon\Text::camelize($url_pars['post_slug']);
                    }
                    if($dispatcher->getControllerName() == 'entity' && $r->hasMethod($childMethod.'Action')){
                        $actionName = $childMethod;
                    }

                    $dispatcher->forward([
                        'controller' => $dispatchableController,
                        'action'     => $actionName,
                        'params'     => $url_pars
                    ]);
                }
            }
        }

        $r = new \ReflectionMethod ( $dispatcher->getHandlerClass (), $dispatcher->getActiveMethod () );
        $method_params = $r->getParameters ();
        if($this->config->application->multisite){
            if(isset($url_pars['application'])){
                $application = \Applicazioni::findFirst([
                    'conditions' => 'codice = ?1',
                    'bind' => [1 => $url_pars['application']],
                    'cache' => [
                        "key" => "getApp".$url_pars['application'],
                        "lifetime" => 120000
                    ]
                ]);
                if(!$application){
                    $this->response->redirect('/'.$this->config->application->defaultCode.'/404');
                } else {
                    \apps\api\library\Cms::getIstance()->id_application = $application->id;
                    \apps\api\library\Cms::getIstance()->application = $application->codice;
                }
            } else {
                \apps\api\library\Cms::getIstance()->id_application = $this->config->application->defaultId;
                \apps\api\library\Cms::getIstance()->application = $this->config->application->defaultCode;
            }
        } else {
            \apps\api\library\Cms::getIstance()->id_application = $this->config->application->defaultId;
            \apps\api\library\Cms::getIstance()->application = $this->config->application->defaultCode;
        }

        $id = 0;
        foreach ( $method_params as $param ) {
            if (!$param->isOptional() && !isset($url_pars[$id ++])){
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