<?php

namespace apps\admin\library\behaviors;

use Phalcon\Mvc\Model\Behavior;
use Phalcon\Mvc\Model\BehaviorInterface;
use Phalcon\Mvc\ModelInterface;

class Timestampable extends Behavior implements BehaviorInterface {
	public function notify($eventType, ModelInterface $model) {
		switch ($eventType) {
			case 'beforeValidationOnCreate' :
				$model->data_creazione = date ( 'Y-m-d H:i:s' );
				break;
			case 'beforeUpdate' :
				$model->data_aggiornamento = date ( 'Y-m-d H:i:s' );
				break;
			
			default :
			/* ignore the rest of events */
		}
	}
}