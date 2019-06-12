<?php

namespace apps\admin\library;

use Phalcon\Mvc\User\Component;

class Auth extends Component
{
    public function login($credential)
    {
        $user = \Utenti::findFirst("nome_utente = '" . $credential ['username'] . "' AND attivo = 1");

        if (!$user) {
            throw new \Exception ('Nome utente o password errati');
        }

        if (!$this->security->checkHash($credential ['password'], $user->password)) {
            throw new \Exception ('Nome utente o password errati');
        }

        $this->check_user($user);
        $this->setIdentity($user);

        if (isset ($credential ['remember'])) {
            $this->create_remember($user);
        }
        $ref = $this->session->get('redirect_after_login', 'admin/index/index');
        $this->session->remove('redirect_after_login');
        apcu_clear_cache();
        apcu_delete('cmsio-acl');

        unlink(APP_DIR . '/cache/acl/data.txt');

        return $this->response->redirect($ref);
    }

    public function check_user(\Utenti $user)
    {
        if ($user->id_tipologia_stato != 1) {
            throw new \Exception ('Utente non attivo');
        }
    }

    private function setIdentity(\Utenti $user)
    {
        $vincoli_assegnazioni = [];
        $gruppi_utenti = $user->getGruppiUtenti([
            'conditions' => 'attivo = 1'
        ]);
        foreach ($gruppi_utenti as $gu) {
            $g = \Gruppi::findFirstById($gu->id_gruppo);
            $vincoli_assegnazioni = array_merge($vincoli_assegnazioni, $g->TipologieGruppo->vincoli_controller);
        }

        $identity = [
            'id'                   => $user->id,
            'id_tipologia_utente'  => $user->id_tipologia_utente,
            'id_ruolo'             => $user->id_ruolo,
            'livello'              => $user->livello,
            'vincoli_assegnazioni' => $vincoli_assegnazioni,
            'nome_utente'          => $user->nome_utente,
            'email'                => $user->email,
            'nome'                 => $user->nome,
            'cognome'              => $user->cognome,
            'ruolo'                => $user->Ruoli->descrizione,
            'avatar'               => $user->avatar
        ];

        $this->session->set('auth-identity', $identity);
    }

    public function create_remember(\Utenti $user)
    {
        $token = $this->create_remember_token($user);

        $user->token = $token;
        $user->data_creazione_token = date('Y-m-d H:i:s');

        if ($user->save() != false) {
            $expire = (time() + (86400 * 7)); // 1 week
            $this->cookies->set('RM_C', json_encode([
                'id'    => $user->id,
                'token' => $token
            ]), $expire);
        }
    }

    public function create_remember_token(\Utenti $user)
    {
        return sha1($user->nome_utente . $user->data_creazione . $user->password . $this->request->getUserAgent());
    }

    public function login_remember()
    {
        $cookie_data = $this->cookies->get('RM_C')->getValue();
        $cd = json_decode(trim($cookie_data));

        $user = \Utenti::findFirstById($cd->id);

        if ($user) {
            $token = $this->create_remember_token($user);

            // verifica che il token nel cookie sia uguale a quello generato, uguale a quello presente nella tabella e che non sia scaduto
            if (($cd->token == $token) && ($cd->token == $user->token) && ((time() - (86400 * 7)) < strtotime($user->data_creazione_token))) {

                $this->check_user($user);
                $this->setIdentity($user);

                $ref = $this->session->get('redirect_after_login', 'admin/index/index');
                $this->session->remove('redirect_after_login');
                return $this->response->redirect($ref);
            }
        }

        $this->cookies->get('RM_C')->delete();

        return $this->response->redirect('admin/session/login');
    }

    public function hasRemember()
    {
        return $this->cookies->has('RM_C');
    }

    public function getIdentity()
    {
        return $this->session->get('auth-identity');
    }

    public function remove()
    {
        if ($this->cookies->has('RM_C'))
            $this->cookies->get('RM_C')->delete();
        $this->session->remove('auth-identity');
    }

    public function getUser()
    {
        if ($this->hasIdentity()) {
            $identity = $this->session->get('auth-identity');
            $user = \Utenti::findFirstById($identity ['id']);
            if (!$user) {
                throw new \Exception ('Utente non trovato');
            } else {
                return $user;
            }
        }

        return false;
    }

    public function hasIdentity()
    {
        return $this->session->has('auth-identity');
    }
}