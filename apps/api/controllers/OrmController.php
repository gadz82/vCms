<?php

/**
 * Created by PhpStorm.
 * User: Francesco
 * Date: 13/03/2019
 * Time: 12:13
 */
class OrmController extends ApiController
{
    public function initialize(){
        parent::initialize();
    }

    public function __call($model, $method){

    }
}