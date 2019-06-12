<?php

class ErrorsController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle(' Oops!');
        parent::initialize();
    }

    public function show401Action()
    {
        echo '401';
        exit();
    }

    public function show404Action()
    {
        $this->tags->setTitle('404');
        $this->tag->setTitle($this->config->application->appName . ' - Contenuto non trovato!');
    }

    public function show500Action()
    {

        //PhalconDebug::debug();

    }
}