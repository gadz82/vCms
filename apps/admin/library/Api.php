<?php

namespace apps\admin\library;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

class Api extends Component {
	private $request;
	public function registerServices(\Phalcon\Mvc\Micro $app, \Phalcon\Http\Request $request) {
		
		/**
		 * Middleware invocato per validazione Token HMAC
		 */
		$app->before ( function () use($request) {
			$this->request = $headers = $request->getHeaders ();
			$authHeader = $headers ['Authentication'];
			try {
				if (strpos ( strtoupper ( $authHeader ), 'HMAC ' ) !== 0)
					throw new \Phalcon\Mvc\Micro\Exception ();
				$authKeySig = substr ( $authHeader, 5 );
				
				if (count ( explode ( ':', $authKeySig ) ) !== 2)
					throw new \Phalcon\Mvc\Micro\Exception ();
				
				list ( $publicKey, $hmacSignature ) = explode ( ':', $authKeySig );
				
				$userApi = \UtentiApi::findFirst ( array (
						array (
								'conditions' => 'public_key = ?1 AND attivo = 1' 
						),
						array (
								'columns' => 'private_key' 
						),
						array (
								'bind' => array (
										1 => $publicKey 
								) 
						) 
				) );
				
				if (! $userApi) {
					throw new \Phalcon\Mvc\Micro\Exception ();
				}
				$requestMethod = $request->getMethod ();
				
				$uri = $request->getURI ();
				
				$rBody = $request->getRawBody ();
				$requestBody = empty ( $rBody ) ? '"' : $rBody;
				
				$payload = '';
				$payload .= $requestMethod . ' ';
				$payload .= $requestBody;
				
				$hmacValue = hash_hmac ( 'sha256', $payload, $userApi->private_key, false );
				
				if ($hmacValue !== $hmacSignature) {
					throw new \Phalcon\Mvc\Micro\Exception ();
				}
			} catch ( \Phalcon\Mvc\Micro\Exception $e ) {
				return false;
			}
		} );
		
		$app->notFound ( function () use($app) {
			$app->response->setStatusCode ( 404, "Not Found" )->sendHeaders ();
			echo 'Endpoint not found!';
		} );
		
		$app->error ( function ($exception) use($app) {
			$app->response->setStatusCode ( 500, "Error" )->sendHeaders ();
			echo "An error has occurred" . PHP_EOL;
			if ($exception instanceof \Exception) {
				echo $exception->getMessage ();
			}
		} );
		
		\app\library\api\ApiAnnunci::register ( $app, $request, $this->di );
		return $app;
	}
}
