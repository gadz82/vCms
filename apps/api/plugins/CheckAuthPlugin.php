<?php

namespace apps\api\plugins;

use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Event;

class CheckAuthPlugin extends Plugin {
	
	/**
	 * Funzione eseguita prima della route
	 *
	 * @param Event $event        	
	 * @param Dispatcher $dispatcher        	
	 */
	public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher) {

	    if($this->request->getMethod() == 'GET') return true;
        
        $headers = $this->request->getHeaders();
        try{
            if(!isset($headers['Authentication'])) throw new \Exception('Permesso negato', 403);
            if(!isset($headers['Cmssignage'])) throw new \Exception('Permesso non verificabile', 403);

            $authHeader = $headers['Authentication'];
            $authSignage = $headers['Cmssignage'];

            if (strpos(strtoupper($authHeader), 'DESEGNO.CMSIO.HMAC~') !== 0)throw new \Exception('Hmac Mancante', 403);
            $authKeySig = substr($authHeader, 19);

            if (count(explode(':', $authKeySig)) !== 2) throw new \Exception('Sign component Mancante', 403);

            list($publicKey, $hmacSignature) = explode(':', $authKeySig);

            $userApi = \Utenti::findFirst([
                'conditions'=> 'public_key = ?1 AND attivo = 1',
                'bind' => [
                    1 => $publicKey
                ]
            ]);

            if(!$userApi){
                throw new \Exception('Public key Errata');
            }

            if(md5($userApi->nome_utente.'.'.$userApi->public_key) !== $authSignage){
                throw new \Exception('Signage Component Errato '.$userApi->nome_utente.'. .'.$userApi->public_key.' -> '.$authSignage, 403);
            }

            $requestMethod = $this->request->getMethod();
            $uri = $this->request->getURI();

            $rBody = $this->request->getRawBody();
            $requestBody = empty($rBody) ? "''" : $rBody;

            $payload = '';
            $payload .= $requestMethod.' ';

            if($this->request->getMethod() == 'POST'){
                $pl = json_decode($requestBody, true);
                if(isset($pl['image'])) unset($pl['image']);
                $payload .= json_encode($pl);
            } else {
                $payload .= $requestBody;
            }

            $hmacValue = hash_hmac('sha256', stripslashes(self::removeUnicodeSequences($payload)), $userApi->private_key, false);
            if ($hmacValue !== $hmacSignature) {
                throw new \Exception('Verifica firma errata', 403);
            }

        } catch(\Exception $exception){
            if(APPLICATION_ENV == 'development') return true;
            $this->response->setStatusCode($exception->getCode());
            $this->response->setJsonContent([
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode()
                ]
            );
            $this->response->send();
            exit();
        }

	}
}