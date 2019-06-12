<?php

namespace apps\site\library;

use Phalcon\Mvc\User\Component;

class Auth extends Component
{


    public function check_user(\Utenti $user)
    {
        if ($user->id_tipologia_stato != 1) {
            throw new \Exception ('Utente non attivo');
        }
    }

    public function setIdentity(\Users $user)
    {
        $identity = [
            'id'                => $user->id,
            'id_tipologia_user' => $user->id_tipologia_user,
            'id_users_groups'   => $user->id_users_groups,
            'username'          => $user->username,
            'email'             => $user->email,
            'nome'              => $user->nome,
            'cognome'           => $user->cognome
        ];

        $this->session->set($this->config->sessionKey, $identity);
    }

    public function getIdentity()
    {
        return $this->session->get($this->config->sessionKey);
    }

    public function isUserLoggedIn()
    {
        return $this->session->has($this->config->sessionKey);
    }

    public function remove()
    {
        if ($this->cookies->has('GRM_C'))
            $this->cookies->get('GRM_C')->delete();
        $this->session->remove($this->config->sessionKey);
    }

    public function getUser()
    {
        if ($this->hasIdentity()) {
            $identity = $this->session->get($this->config->sessionKey);
            $user = \Users::findFirstById($identity ['id']);
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
        return $this->session->has($this->config->sessionKey);
    }

    public function create_remember_token(\Users $user)
    {
        return sha1($user->username . $user->data_creazione . $user->password . $this->request->getUserAgent());
    }
}