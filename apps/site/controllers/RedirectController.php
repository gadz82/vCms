<?php

class RedirectController extends \Phalcon\Mvc\Controller
{

    private static $redirect = [
        /*'from-url' => '/to-url'
        'alfaromeo' => '/auto/list/marca-alfa_romeo'*/
    ];

    public function executeAction()
    {
        $route = $this->dispatcher->getParam('route');
        if (!$route || empty($route) || !array_key_exists($route, self::$redirect)) {
            $this->response->redirect('/404', false, 404);
        } else {
            $this->response->redirect(self::$redirect[$route], false, 301);
        }
    }
}