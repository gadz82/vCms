<?php

namespace apps\admin\library\behaviors;

use Phalcon\Mvc\Model\Behavior;
use Phalcon\Mvc\Model\BehaviorInterface;
use Phalcon\Mvc\ModelInterface;

class UserSession extends Behavior implements BehaviorInterface
{
    public function notify($eventType, ModelInterface $model)
    {
        switch ($eventType) {
            case 'beforeCreate' :
            case 'beforeValidation' :
            case 'beforeUpdate' :
            case 'beforeSave' :
            case 'beforeDelete' :
                $metaData = $model->getModelsMetaData();
                $fields = $metaData->getAttributes($model);

                if ($metaData->hasAttribute($model, 'id_utente')) {

                    $session = $model->getDI()->getSession();
                    if ($session->has('auth-identity')) {
                        $auth = $session->get('auth-identity');
                        $model->id_utente = $auth ['id'];
                    }
                }

                break;

            default :
                /* ignore the rest of events */
        }
    }
}