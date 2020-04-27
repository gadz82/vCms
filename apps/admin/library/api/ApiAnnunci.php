<?php

namespace app\library\api;

use Phalcon\Http\Response;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;
use Phalcon\Http\Request;
use Phalcon\Mvc\User\Component;
use Phalcon;

class ApiAnnunci extends Component {
	public static function register(\Phalcon\Mvc\Micro $app, \Phalcon\Http\Request $request, \Phalcon\DI $di) {
		$app->post ( '/annunci/create', function () use($app, $request, $di) {
			
		});
	}
} 