<?php

namespace apps\site\plugins;

use apps\site\library\Cms;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Event;

class SecurityPlugin extends Plugin
{

    /**
     * @param Event $event
     * @param Dispatcher $dispatcher
     * @return bool
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        $auth = $this->session->get($this->config->sessionKey);
        Cms::getIstance()->adminLoggedIn = $this->session->get($this->config->sessionKeyAdmin);

        if (!$auth || is_null($auth)) {
            Cms::getIstance()->userLoggedIn = false;
        } else {
            $this->view->auth_user = $auth;
            Cms::getIstance()->userLoggedIn = true;
        }
    }
}