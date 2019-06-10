<?php

class UsersController extends ControllerBase
{

    public function initialize(){
        parent::initialize();
    }

    /**
     *  Form Registrazione e Login
     */
    public function indexAction(){
        $this->tags->setTitle('Accedi o Registrati');
        $this->tags->setRobots('noindex,follow');
        if($this->isUserLoggedIn){
            return $this->response->redirect('/user/viewProfile');
        } else {
            $fb = new \Facebook\Facebook([
                'app_id' => $this->config->facebook['appId'], // Replace {app-id} with your app id
                'app_secret' => $this->config->facebook['appSecret'],
                'default_graph_version' => 'v3.2',
            ]);
            $helper = $fb->getRedirectLoginHelper();
            $permissions = ['email']; // Optional permissions
            $this->view->facebookLoginUrl = $helper->getLoginUrl('https:'.$this->config->application['siteUri'].$this->config->facebook['cbPage'], $permissions);

            $this->view->csrfTokenKey = $this->security->getTokenKey();
            $this->view->csrfToken = $this->security->getToken();
            if(is_null($this->session->get('site_redirect_after_login'))){
                $this->session->set('site_redirect_after_login', $this->currentUrl);
            }
            $this->assets->addJs('assets/site/js/user/register-login.js');
        }
    }


    /**
     * Form Recupero Password, stessa route per form per richiesta di cambio password e request ajax
     *
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function passwordLostAction(){
        /*
         * Request reset della passwrod
         */
        if($this->request->isPost() && $this->request->isAjax()){
            $params = $this->request->getPost();
            /*
             * Validazione CSRF
             */
            if(!isset($params['email'])){
                $this->response->setJsonContent(['success' => false, 'content' => 'Ci serve l\'indirizzo email che hai usato per la registrazione']);
            }
            $security = \Phalcon\Di::getDefault()->get('security');
            if (!$security->checkToken()) {
                return $this->response->setJsonContent(['success' => false, 'content' => 'Richiesta non valida']);
            }

            // Recupero l'utente in stato attivo con la mail inserita
            $user = Users::findFirst([
                'conditions' => 'email = :email: AND id_tipologia_stato = 1 AND token_validated = 1 AND attivo = 1',
                'bind' => ['email' => $params['email']]
            ]);
            if(!$user ){

                return $this->response->setJsonContent(['success' => false, 'content' => 'Account non trovato o in attesa di attivazione dopo la registrazione']);

            } else {
                /*
                 * Generazione token univoco per il link di reset password e relativa data di scadenza ad 1 giorno
                 */
                $user->password_reset_token = sha1(mt_rand(10000,99999).time().$params['email']);
                $user->reset_password_expiration_date= (new DateTime())->add(new DateInterval('P1D'))->format('Y-m-d H:i:s');

                if(!$user->save()){
                    $this->flash->error('Errore nel processo di invio token');
                }

                /*
                 * Invio mail per reset password
                 */
                $this->mailer->send(
                    $params['email'],
                    [],
                    ['francesco@desegno.it'],
                    'Link per il cambio password su '.$this->config->application->appName,
                    'password-lost',
                    [
                        'nome' => $user->nome.' '.$user->cognome,
                        'tokenUrl' => 'https:'.$this->config->application['siteUri'].'/user/resetPassword/'.$user->id.'/'.$user->password_reset_token
                    ],
                    [],
                    true
                );
                PhalconDebug::debug($this->config->application['siteUri'].'/user/resetPassword/'.$user->id.'/'.$user->password_reset_token);

                /*
                 * Response Ajax
                 */
                return $this->response->setJsonContent([
                    'success' => true,
                    'content' => 'Ti abbiamo inviato una mail con il link per il reset della password'
                ]);
            }

        } else {
            /*
             * Form e chiavi csrf
             */
            if($this->isUserLoggedIn){
                $this->response->redirect('/user');
            }
            $this->view->csrfTokenKey = $this->security->getTokenKey();
            $this->view->csrfToken = $this->security->getToken();
            $this->assets->addJs('assets/site/js/user/password-lost.js');
        }
    }


    /**
     * Form Visualizza / Modifica Profilo
     */
    public function viewProfileAction(){
        if(!$this->isUserLoggedIn){
            return $this->response->redirect('/user');
        }
        $this->tags->setTitle('Il Mio Account');
        if(!$this->isUserLoggedIn || empty($u = $this->user)){
            return $this->response->redirect('/user');
        }

        $user = Users::findFirst([
            'conditions' => 'id = :user_id: AND id_tipologia_stato = 1 AND attivo = 1',
            'bind' => ['user_id' => $u['id']]
        ]);

        if(!$user) return $this->response->redirect('/user');

        $this->view->user = $user;
        $this->view->csrfTokenKey = $this->security->getTokenKey();
        $this->view->csrfToken = $this->security->getToken();
        $this->assets->addJs('assets/site/js/components/datepicker.js');
        $this->assets->addJs('assets/site/js/user/user-profile.js');
        $this->assets->addCss('assets/site/css/components/datepicker.css');
    }


    /**
     * Request Modifica Profilo utente
     */
    public function editProfileAction(){
        if(!$this->isUserLoggedIn){
            return $this->response->redirect('/user');
        }
        if(!$this->request->isPost()){
            $this->flashSession->error('Impossibile eseguire la modifica del profilo');
            return $this->response->redirect('/user');
        }

        $params = $this->request->getPost();

        if(!isset($params['nome'],$params['cognome'],$params['email'],$params['username'],$params['id_user'])){
            $this->flashSession->error('Richiesta non valida, parametri mancanti');
            return $this->response->redirect('/user');
        }

        /*
         * @var \Phalcon\Security
         */
        $security = \Phalcon\Di::getDefault()->get('security');
        if (!$security->checkToken()) {
            $this->flashSession->error('Richiesta non valida');
            return $this->response->redirect('/user');
        }

        $user = Users::findFirst([
            'conditions' => 'id = ?1 AND attivo = 1',
            'bind' => [1 => $params['id_user']]
        ]);

        if(!$user){
            $this->flashSession->error('Richiesta non valida, utete non trovato');
            return $this->response->redirect('/user');
        }

        $user->username = $params['username']  ;
        $user->email = $params['email'];
        $user->nome = $params['nome'];
        $user->cognome = $params['cognome'];

        if(!empty($params['old-password'])){
            if(!$this->security->checkHash ( $params['old-password'], $user->password )){
                $this->flashSession->error('Vecchia Password errata');
                return $this->response->redirect('/user');
            }
            if(isset($params['new-password'], $params['new-repassword'])){
                if($params['new-password'] !== $params['new-repassword']){
                    $this->flashSession->error('Non hai ripetuto la nuova password in maniera corretta.');
                    return $this->response->redirect('/user');
                }
                $user->password = $this->security->hash ($params['new-password']);
            }
        }


        if(!empty($params['telefono'])) $user->telefono = $params['telefono'];
        if(!empty($params['indirizzo'])) $user->indirizzo = $params['indirizzo'];
        if(!empty($params['localita'])) $user->localita = $params['localita'];
        if(!empty($params['cap'])) $user->cap = $params['cap'];
        if(!empty($params['data_di_nascita'])) $user->data_di_nascita = $params['data_di_nascita'];

        if(!$user->save()){
            $this->flashSession->error('Si è verificato un errore durante il salvataggio');
            foreach($user->getMessages() as $message){
                PhalconDebug::debug($message->getMessage());
            }
            return $this->response->redirect('/user');
        }
        $this->flashSession->success('Profilo modificato con successo');
        return $this->response->redirect('/user');
    }


    /**
     * Request Recupero Password
     * Gestisce sia la request ajax per il cambio password che il form
     */
    public function resetPasswordAction($userId = null, $password_token = null){
        if($this->request->isPost() && $this->request->isAjax()){
            $params = $this->request->getPost();
            if(
                !isset($params['password'],$params['reset-form-repassword'], $params['id_user']) ||
                $params['password'] !== $params['reset-form-repassword']
            ){
                $this->response->setJsonContent(['success' => false, 'content' => 'Password non valida']);
            }

            /*
             * @var \Phalcon\Security
             */
            $security = \Phalcon\Di::getDefault()->get('security');
            if (!$security->checkToken()) {
                return $this->response->setJsonContent(['success' => false, 'content' => 'Richiesta non valida']);
            }

            $user = Users::findFirst([
                'conditions' => 'id = :id: AND id_tipologia_stato = 1 AND attivo = 1',
                'bind' => ['id' => $params['id_user']]
            ]);
            if(!$user){

                return $this->response->setJsonContent(['success' => false, 'content' => 'Account non trovato, richiesta non valida']);

            } else {
                $user->password = $this->security->hash ($params['password']);

                if(!$user->save()){
                    return $this->response->setJsonContent(['success' => false, 'content' => 'Richiesta non valida']);
                }

                return $this->response->setJsonContent([
                    'success' => true,
                    'content' => 'Password aggiornata correttamente, <a href="/user">clicca qui</a> se non vieni reindirizzato automaticamente.'
                ]);
            }
        } else{
            /*
             * Verifica chiave token dall'url e reset del valore nel db per invalidare successive request
             */
            if($this->isUserLoggedIn){
                return $this->response->redirect('/user');
            }
            if(!is_null($userId) && !is_null($password_token)){

                $user = Users::findFirstById($userId);
                if(!$user){
                    $this->flash->error('L\'utente è stato disattivato o non esiste più.');
                    $this->dispatcher->forward(['action' => 'index']);
                }

                //Verifica token e expiration
                if($password_token != $user->password_reset_token || (new \DateTime())->format('Y-m-d H:i:s') > $user->reset_password_expiration_date){
                    $this->flash->error('Token per il reset password non valido, già utilizzato o scaduto, richiedi una nuova password qui sotto.');
                    $this->dispatcher->forward(['action' => 'passwordLost']);
                } else {
                    /*
                     * Decommentare
                     */
                    $user->password_reset_token = '';

                    if(!$user->save()){
                        $this->flash->error('Errore nel processo di attivazione account');
                    }
                    $this->view->csrfTokenKey = $this->security->getTokenKey();
                    $this->view->csrfToken = $this->security->getToken();
                    $this->view->id_user = $userId;
                    $this->assets->addJs('assets/site/js/user/reset-password.js');
                }
            } else {
                return $this->response->redirect('/user');
            }
        }
    }


    /**
     * Request di Login in post
     */
    public function loginAction(){
        if($this->request->isPost()){
            $params = $this->request->getPost();
            if(!isset($params['username'], $params['password'])){
                $this->flashSession->error('Parametri mancanti');
                $this->response->redirect('/user');
            }
            /*
             * @var \Phalcon\Security
             */
            $security = \Phalcon\Di::getDefault()->get('security');
            if (!$security->checkToken()) {
                $this->flashSession->error('Richiesta non Valida');
                return $this->response->redirect('/user');
            }

            $user = \Users::findFirst ( "username = '" . $params ['username'] . "' AND attivo = 1" );

            if (! $user) {
                $this->flashSession->error('Nessun utente con questo username');
                return $this->response->redirect('/user');
            }

            //Verifica password
            if (! $this->security->checkHash ( $params ['password'], $user->password )) {
                $this->flashSession->error('Password Errata');
                return $this->response->redirect('/user');
            }

            if ($user->id_tipologia_stato != 1) {
                $this->flashSession->error('Utente non attivo, se hai non hai ricevuto il link di attivazione <a href="/user/sendActivation"></a>');
            }
            $user->user_last_login = (new \DateTime())->format('Y-m-d H:i:s');
            $user->ip_address = $this->request->getClientAddress();
            $this->auth->setIdentity ( $user );
            $user->save();

            $ref = $this->session->get('site_redirect_after_login', '/user');
            return $this->response->redirect ( $ref );

        } else {
            return $this->response->redirect('/user');
        }

    }


    /**
     * Logout
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function logoutAction(){
        if($this->isUserLoggedIn){
            $this->auth->remove();
        }
        return $this->response->redirect ( '/user' );
    }


    /**
     * Request di registrazione
     */
    public function registrationAction(){
        $now = new \DateTime();
        if(!$this->request->isPost() || !$this->request->isAjax()){
            $this->response->redirect('/user');
        }
        $params = $this->request->getPost();

        if(!isset($params['nome'],$params['cognome'],$params['email'],$params['password'], $params['register-form-repassword'])){
            $this->response->setJsonContent(['success' => false, 'content' => 'Parametri errati']);
            return $this->response;
        }
        /*
         * @var \Phalcon\Security
         */
        $security = \Phalcon\Di::getDefault()->get('security');
        if (!$security->checkToken()) {
            $this->response->setJsonContent(['success' => false, 'content' => 'Richiesta non valida']);
            return $this->response;
        }

        $user = Users::findFirst([
            'conditions' => '( email = :email: OR username = :email: ) AND attivo = 1',
            'bind' => ['email' => $params['email']]
        ]);

        if(!$user){
            $user = new Users();
            $user->id_users_groups = 2;
            $user->id_tipologia_stato = 2;
            $user->id_tipologia_user = 1;
            $user->username = $params['email']  ;
            $user->email = $params['email'];
            $user->nome = $params['nome'];
            $user->cognome = $params['cognome'];
            $user->validation_token = sha1(mt_rand(10000,99999).time().$params['email']);
            $user->token_validated = 0;
            $user->validation_expiration_date = $now->add(new DateInterval('P1D'))->format('Y-m-d H:i:s');;
            $user->password = $this->security->hash ($params['password']);
            $user->user_registration_date = $now->format('Y-m-d H:i:s');
            $user->ip_address = $this->request->getClientAddress();
            $user->data_creazione = $now->format('Y-m-d H:i:s');
            $user->data_aggiornamento = $now->format('Y-m-d H:i:s');
            $user->attivo = 1;
        } else {
            switch($user->id_tipologia_stato){
                // UTENTE GIA ATTIVO
                case 1:
                    return $this->response->setJsonContent(['success' => false, 'content' => 'Utente già registrato, effettua l\'accesso o recupera la password']);
                    break;
                // UTENTE IN ATTESA DI ATTIVAZIONE
                case 2:
                    return $this->response->setJsonContent([
                        'success' => false,
                        'content' => 'Abbiamo inviato un link al tuo indirizzo email per l\'attivazione dell\'account. Se non l\'hai ricevuto contattaci o <a href="/user/sendActivation">clicca qui</a>'
                    ]);
                    break;
                case 3:
                    return $this->response->setJsonContent([
                        'success' => false,
                        'content' => 'Account già registrato, in attesa di reset della password'
                    ]);
                    break;
                case 5:
                    return $this->response->setJsonContent([
                        'success' => false,
                        'content' => 'Account già registrato, bloccato dall\'amministratore'
                    ]);
                    break;
                case 4:
                    $user->id_users_groups = 2;
                    $user->id_tipologia_stato = 2;
                    $user->id_tipologia_user = 1;
                    $user->username = $params['email'];
                    $user->email = $params['email'];
                    $user->nome = $params['nome'];
                    $user->cognome = $params['cognome'];
                    $user->validation_token = sha1(mt_rand(10000,99999).time().$email);
                    $user->token_validated = 0;
                    $user->validation_expiration_date = $now->add(new DateInterval('P1D'))->format('Y-m-d H:i:s');
                    $user->password = $this->security->hash ($params['password']);
                    $user->ip_address = $this->request->getClientAddress();
                    $user->attivo = 1;
                    break;
            }
        }
        if(!$user->save()){
            foreach ($user->getMessages() as $msg){
                \PhalconDebug::debug($msg->getMessage());
            }
            return $this->response->setJsonContent(['success' => false, 'content' => 'Errore in fase di registrazione dell\'account.']);
        }

        PhalconDebug::debug($this->mailer->send(
            $params['email'],
            [],
            ['francesco@desegno.it'],
            'Attiva il tuo account su '.$this->config->application->appName,
            'attivazione',
            [
                'nome' => $user->nome.' '.$user->cognome,
                'tokenUrl' => 'https:'.$this->config->application['siteUri'].'/user/activateAccount/'.$user->id.'/'.$user->validation_token
            ],
            [],
            true
        ));
        return $this->response->setJsonContent([
            'success' => true,
            'content' => 'Registrazione effettuata, ti abbiamo inviato una mail con il link di attivazione dell\'account'
        ]);
    }


    /**
     * Request di registrazione / Login tramite Facebook
     */
    public function facebookRegistrationAction(){
        $now = new \DateTime();
        $fb = new \Facebook\Facebook([
            'app_id' => $this->config->facebook['appId'], // Replace {app-id} with your app id
            'app_secret' => $this->config->facebook['appSecret'],
            'default_graph_version' => 'v3.2',
        ]);

        /*
         * STEP NR.1
         * Recupero l'access Token
         */
        $helper = $fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken('https:'.$this->config->application['siteUri'].$this->config->facebook['cbPage']);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $this->flashSession->error('Impossibile effettuare l\'accesso attraverso il tuo account Facebook');
            return $this->response->redirect($this->session->get('site_redirect_after_login', 'site/user'));
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            $this->flashSession->error('Facebook sembra non rispondere, prova a registrarti attraverso il sito.');
            return $this->response->redirect($this->session->get('site_redirect_after_login', 'site/user'));
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                $this->flashSession->error('Accesso negato.');
                return $this->response->redirect($this->session->get('site_redirect_after_login', 'site/user'));
                /*header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";*/
            } else {
                $this->flashSession->error('Servizio momentaneamente non disponibile.');
                return $this->response->redirect($this->session->get('site_redirect_after_login', 'site/user'));
            }
        }

        /*
         * STEP NR.2
         * Validazione dell'access token e recupero informazioni di scadenza
         */
        $oAuth2Client = $fb->getOAuth2Client();
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        $tokenMetadata->validateAppId($this->config->facebook['appId']); // Replace {app-id} with your app id
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
            // Se l'access token scade a breve lo prolungo (magari per un utilizzo futuro)
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                $this->flashSession->error('Abbiamo incontrato un problema nel recupero delle tue informazioni da Facebook.');
                return $this->response->redirect($this->session->get('site_redirect_after_login', 'site/user'));
            }
        }

        /*
         * STEP NR.3
         * Utilizzo l'access token per recuperare le informazioni sull'utente
         */
        try {
            $response = $fb->get('/me?fields=id,name,first_name,last_name,email,address,birthday', $accessToken);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            $this->flashSession->error('Impossibile recuperare le informazioni dal tuo profilo.');
            return $this->response->redirect($this->session->get('site_redirect_after_login', 'site/user'));
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            $this->flashSession->error('Abbiamo incontrato un problema nel recupero delle tue informazioni da Facebook.');
            return $this->response->redirect($this->session->get('site_redirect_after_login', 'site/user'));
        }
        //Oggetto utente
        $fbUser = $response->getGraphUser();

        /*
         * STEP NR.4
         * Verifico se si tratta di un nuovo utente o di un utente che si è già registrato
         * Poi effettuo un create o un update
         */
        $existingUser = Users::findFirst([
            'conditions' => 'facebook_auth_id = :facebook_user_id: AND attivo = 1',
            'bind' => ['facebook_user_id' => $fbUser->getId()]
        ]);

        if(!$existingUser){
            if(is_null($email = $fbUser->getEmail())){
                $this->flashSession->error('Indirizzo email mancante nel tuo profilo Facebook.');
                return $this->response->redirect($this->session->get('site_redirect_after_login', 'site/user'));
            }

            /*
             * Inizio registrazione
             */
            $user = new Users();
            $user->id_users_groups = 2;
            $user->id_tipologia_stato = 1;
            $user->id_tipologia_user = 2;
            $user->username = $email;
            $user->email = $email;
            $user->nome = $fbUser['first_name'];
            $user->cognome = $fbUser['last_name'];
            if(!is_null($fbUser->getBirthday())){
                $user->data_di_nascita = $fbUser->getBirthday()->format('Y-m-d');
            }
            $user->token_validated = 1;
            $user->password = '';
            $user->user_registration_date = $now->format('Y-m-d H:i:s');
            $user->user_last_login = $now->format('Y-m-d H:i:s');
            $user->ip_address = $this->request->getClientAddress();
            $user->facebook_auth_id = $fbUser->getId();
            $user->facebook_auth_token = (string) $accessToken;
            $user->data_creazione = $now->format('Y-m-d H:i:s');
            $user->data_aggiornamento = $now->format('Y-m-d H:i:s');
            $user->attivo = 1;

            if(!$user->save()){
                $this->flashSession->error('Errore in fase di registrazione, esiste già un utente con questo indirizzo email, controlla i dati o effettua un recupero password.');
                foreach ($user->getMessages() as $lm){
                    PhalconDebug::debug($lm->getMessage());
                }
                return $this->response->redirect($this->session->get('site_redirect_after_login', 'site/user'));
            }
            /*
             * Creo la sessione utente
             */
            $this->auth->setIdentity($user);
            $this->flashSession->success('Accesso effettuato, benvenuto '.$fbUser->getName());
            return $this->response->redirect($this->session->get('site_redirect_after_login', '/user'));

        } else {

            $existingUser->facebook_auth_token = (string) $accessToken;
            $existingUser->user_last_login = $now->format('Y-m-d H:i:s');
            $existingUser->id_tipologia_stato = 1;
            $existingUser->token_validated = 1;

            if(!$existingUser->save()){
                $this->flashSession->error('Errore in fase di autenticazione, riprova in un altro momento.');
                foreach ($existingUser->getMessages() as $lm){
                    PhalconDebug::debug($lm->getMessage());
                }
                return $this->response->redirect($this->session->get('site_redirect_after_login', 'site/user'));
            }

            /*
             * Creo la sessione utente
             */
            $this->auth->setIdentity($existingUser);
            $this->flashSession->success('Accesso effettuato, benvenuto '.$fbUser->getName());
            return $this->response->redirect($this->session->get('site_redirect_after_login', 'site/user'));
        }

    }


    /**
     * Attivazione di un account registrato dal sito, tramite il link inviato via email
     * @param $userId
     * @param $activationToken
     */
    public function activateAccountAction($userId, $activationToken){

        $user = Users::findFirstById($userId);
        if(!$user){
            $this->flash->error('Link di attivazione non valido, ripeti la registrazione o contattaci.');
            $this->view->activeBlock = 'lost-user';
            $this->dispatcher->forward(['action' => 'index']);
        }

        //Verifica requisiti logici
        if(
            $user->id_tipologia_user != 1 || //Attivazione necessaria solo per gli utenti registrati dal sito
            $user->id_tipologia_stato != 2 || //Attivazione necessaria solo per gli utenti in attesa di attivazione
            $user->token_validated == 1 || //Attivazione necessaria solo per gli utenti con flag token_validated a 1
            empty($user->validation_token)
        ) {

            $this->flash->error('Il tuo account è stato già attivato in precedenza, effettua l\'accesso o recupera la password');
            $this->dispatcher->forward(['action' => 'index']);

        } elseif($activationToken != $user->validation_token || (new \DateTime())->format('Y-m-d H:i:s') > $user->validation_expiration_date){
            $this->flash->error('Token di attivazione non valido o scaduto');
            $this->view->activeBlock = 'new-token';
            $this->dispatcher->forward(['action' => 'sendActivation']);
        } else {
            $user->validation_token = '';
            $user->token_validated = 1;
            $user->id_tipologia_stato = 1;

            if(!$user->save()){
                $this->flash->error('Errore nel processo di attivazione account');
            } else {
                $this->flash->success('Account attivato con successo, accedi con le credenziali che hai scelto in fase di registazione.');
            }
            $this->dispatcher->forward(['action' => 'index']);
        }
    }


    /**
     * Reinvio link attivaziona account dopo tentativo di login di utenti non attivi
     * Gestisce sia il rendering del form per la richiesta link di attivazione, che la relativa request e invio email
     */
    public function sendActivationAction(){
        if($this->request->isPost() && $this->request->isAjax()){
            $params = $this->request->getPost();
            /*
             * @var \Phalcon\Security
             */
            if(!isset($params['email'])){
                $this->response->setJsonContent(['success' => false, 'content' => 'Ci serve l\'indirizzo email che hai usato per la registrazione']);
            }
            $security = \Phalcon\Di::getDefault()->get('security');
            if (!$security->checkToken()) {
                return $this->response->setJsonContent(['success' => false, 'content' => 'Richiesta non valida']);
            }

            $pendingUser = Users::findFirst([
                'conditions' => 'email = :email: AND id_tipologia_stato = 2 AND token_validated = 0 AND attivo = 1',
                'bind' => ['email' => $params['email']]
            ]);
            if(!$pendingUser){

                return $this->response->setJsonContent(['success' => false, 'content' => 'Account non trovato o già attivato in precedenza']);

            } else {

                $pendingUser->validation_token = sha1(mt_rand(10000,99999).time().$params['email']);
                $pendingUser->validation_expiration_date = (new DateTime())->add(new DateInterval('P1D'))->format('Y-m-d H:i:s');

                if(!$pendingUser->save()){
                    return $this->response->setJsonContent(['success' => false, 'content' => 'Errore nel processo di invio token']);
                }

                $this->mailer->send(
                    $params['email'],
                    [],
                    ['francesco@desegno.it'],
                    'Link attivazione account su '.$this->config->application->appName,
                    'attivazione',
                    [
                        'nome' => $pendingUser->nome.' '.$pendingUser->cognome,
                        'tokenUrl' => 'https:'.$this->config->application['siteUri'].'/user/activateAccount/'.$pendingUser->id.'/'.$pendingUser->validation_token
                    ],
                    [],
                    true
                );

                return $this->response->setJsonContent([
                    'success' => true,
                    'content' => 'Ti abbiamo reinviato il link per l\'attivazione del tuo account'
                ]);
            }

        } else {
            $this->view->csrfTokenKey = $this->security->getTokenKey();
            $this->view->csrfToken = $this->security->getToken();
            $this->assets->addJs('assets/site/js/user/send-activation.js');
        }
    }

    public function deleteAction(){
        if(!$this->isUserLoggedIn){
            return $this->response->redirect('/user/viewProfile');
        }
        $user = $this->user;
        if($user->id && $su = Users::findFirstById($user->id)){
            if(!$su->delete()){
                $this->flashSession->error('Errore in fase di cancellazione');
                return $this->response->redirect('/user/viewProfile');
            } else {
                $this->flashSession->success('Utente eliminato');
                return $this->response->redirect('/user');
            }
        } else {
            $this->flashSession->error('Utente non trovato');
            return $this->response->redirect('/user/viewProfile');
        }
    }

    protected function setAuthVars(){
        if(\apps\site\library\Cms::getIstance()->userLoggedIn){
            $this->view->isUserLoggedIn = $this->isUserLoggedIn = true;
            $this->view->user= $this->user = $this->auth->getIdentity();
        } else {
            $this->view->isUserLoggedIn = $this->isUserLoggedIn = false;
        }

        $this->view->isAdminLoggedIn = $this->isAdminLoggedIn = \apps\site\library\Cms::getIstance()->adminLoggedIn;
    }

}