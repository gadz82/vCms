<?php

namespace apps\admin\plugins;

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Event;

class SecurityPlugin extends Plugin
{
    /**
     * @var SecurityPlugin
     */
    private static $istance;
    private $acl;
    private $filePath = '/cache/acl/data.txt';
    private $public_resources = [
        'session' => [
            'index',
            'login'
        ],
        'errors'  => [
            'show401',
            'show404',
            'show500'
        ],
        'Asset'   => [
            'js',
            'css'
        ]
    ];

    public function __construct($wl = [])
    {
        $this->public_resources = $wl;
        self::$istance = $this;
    }

    public static function getIstance()
    {
        if (self::$istance instanceof SecurityPlugin) {
            return self::$istance;
        }
    }

    /**
     * @param Event $event
     * @param Dispatcher $dispatcher
     * @return bool
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        $controller = $dispatcher->getControllerName();

        $action = $dispatcher->getActionName();

        $acl = $this->getAcl();
        $auth = $this->session->get('auth-identity');
        if (!$this->isPublic_resources($controller)) {


            if (!$auth) {
                if ($this->auth->hasRemember()) {
                    return $this->auth->login_remember();
                } else {
                    $this->session->set('redirect_after_login', $_SERVER['REQUEST_URI']);
                    return $this->response->redirect('admin');
                }
            }

            $this->view->auth_user = $auth;
            $this->view->auth_menu = $this->getMenu($auth ['id_ruolo'], $auth ['livello']);

            if (in_array($action, [
                'index',
                'search',
                'new',
                'create',
                'edit',
                'save',
                'delete'
            ])) {
                if ($acl->isAllowed($auth ['ruolo'] . '[' . $auth ['livello'] . ']', $controller, $action) != Acl::ALLOW) {

                    if ($acl->isAllowed($auth ['ruolo'], $controller, 'index') != Acl::ALLOW) {
                        $this->flash->outputMessage('plain error', 'Attenzione! Non disponi dei permessi per accedere al modulo "' . $controller . ' / ' . $action . '"');
                        $dispatcher->forward([
                            'controller' => 'errors',
                            'action'     => 'show401'
                        ]);
                    } else {
                        $this->flashSession->error("Attenzione! Non disponi dei permessi per accedere al modulo " . $controller . " / " . $action);
                        $dispatcher->forward([
                            'controller' => $controller,
                            'action'     => 'index'
                        ]);
                    }

                    return false;
                }
            }
        }
    }

    public function getAcl()
    {

        // $this->acl = $this->rebuild();
        if (is_object($this->acl))
            return $this->acl;

        // Verifica se l'ACL � in APC
        if (function_exists('apcu_fetch')) {
            $acl = apcu_fetch('cmsio-acl');
            if (is_object($acl)) {
                $this->acl = $acl;
                if (!$this->session->has('acl'))
                    $this->session->set('acl', $this->acl);
                return $acl;
            }
        }

        // Verifica se esiste il file ACL in cache
        if (!file_exists(APP_DIR . $this->filePath)) {
            $this->acl = $this->rebuild();
            return $this->acl;
        }

        // Estra i dati dall'ACL in cache
        $data = file_get_contents(APP_DIR . $this->filePath);
        $this->acl = unserialize($data);

        // Salva l'ACL in APC
        if (function_exists('apcu_store')) {
            apcu_store('cmsio-acl', $this->acl);
        }

        return $this->acl;
    }

    public function rebuild()
    {
        $acl = new AclMemory ();
        $acl->setDefaultAction(Acl::DENY);

        // Estra i ruoli e li aggiunge all'ACL
        $utenti = \Utenti::find([
            'group' => 'id, id_ruolo, livello'
        ]);
        foreach ($utenti as $utente) {
            $acl->addRole(new Role (\Ruoli::findFirstById($utente->id_ruolo)->descrizione . '[' . $utente->livello . ']'));
        }

        // Aggiunge le risorse private all'ACL
        $risorse = \RuoliPermessi::find([
            'attivo = 1',
            'group' => 'id,risorsa, azione'
        ]);
        $arr_risorse = [];
        foreach ($risorse as $r) {
            if (array_key_exists($r->risorsa, $arr_risorse)) {
                $arr_risorse [$r->risorsa] = array_merge($arr_risorse [$r->risorsa], json_decode($r->azione));
            } else {
                $arr_risorse [$r->risorsa] = json_decode($r->azione);
            }
        }
        foreach ($arr_risorse as $key => $val) {
            $acl->addResource(new Resource ($key), $val);
        }
        // Aggiunge le risorse e le azioni pubbliche all'ACL
        foreach ($this->public_resources as $risorsa => $azioni) {
            $acl->addResource(new Resource ($risorsa), $azioni);
        }

        // Aggiunge l'accesso alle risorse e alle azioni a tutti i ruoli
        foreach ($utenti as $utente) {

            $ruolo = \Ruoli::findFirstById($utente->id_ruolo)->descrizione;

            // risorse pubbliche
            foreach ($this->public_resources as $risorsa => $azioni) {
                $count = count($azioni);
                for ($i = 0; $i < $count; $i++) {
                    $acl->allow($ruolo . '[' . $utente->livello . ']', $risorsa, $azioni [$i]);
                }
            }

            // risorse private
            $ruoli_permessi = \RuoliPermessi::find([
                'id_ruolo = ' . $utente->id_ruolo . ' AND livello <= ' . $utente->livello . ' AND attivo = 1'
            ]);
            foreach ($ruoli_permessi as $permessi) {
                $acl->allow($ruolo . '[' . $utente->livello . ']', $permessi->risorsa, json_decode($permessi->azione, true));
            }
        }

        // Scrive il file in cache
        if (touch(APP_DIR . $this->filePath) && is_writable(APP_DIR . $this->filePath)) {
            file_put_contents(APP_DIR . $this->filePath, serialize($acl));
            // Salva l'ACL in APC
            if (function_exists('apcu_store'))
                apcu_store('cmsio-acl', $acl);
        } else {
            $this->flash->error('Non hai i permessi di scrittura per ' . APP_DIR . $this->filePath);
        }

        $this->session->set('acl', $acl);

        return $acl;
    }

    public function isPublic_resources($controllerName)
    {
        return isset ($this->public_resources [$controllerName]);
    }

    public function getMenu($id_ruolo, $livello)
    {
        $ruoliMenu = \RuoliMenu::find([
            'conditions' => 'id_ruolo = :ruolo_id: AND livello <= :livello: AND visible = 1 AND attivo = 1',
            'bind'       => [
                'ruolo_id' => $id_ruolo,
                'livello'  => $livello
            ],
            'order'      => 'id_padre ASC, ordine ASC',
            'columns'    => 'id, risorsa, azione, descrizione, class, header, visible, id_padre, ordine',

        ]);
        $post_types = \TipologiePost::find(
            [
                'conditions' => 'admin_menu = 1',
                'order'      => 'ordine ASC',
                'columns'    => 'id, admin_icon, descrizione',
                'cache'      => [
                    "key"      => "MenuPostTypesFind" . $id_ruolo . '-' . $livello,
                    "lifetime" => 12400
                ]
            ]
        )->toArray();

        $menu = $ruoliMenu->toArray();

        $add_menu = [];
        if (!empty($post_types)) {
            foreach ($post_types as $pt) {
                $add_menu[] = [
                    "id"          => "9999991" . $pt['id'],
                    "risorsa"     => "",
                    "azione"      => "",
                    "descrizione" => $pt['descrizione'],
                    "class"       => str_replace('fa ', '', $pt['admin_icon']),
                    "header"      => "0",
                    "visible"     => "1",
                    "id_padre"    => "0",
                    "ordine"      => "3"
                ];
                $add_menu[] = [
                    "id"          => "9999992" . $pt['id'],
                    "risorsa"     => "posts",
                    "azione"      => "index/" . $pt['id'],
                    /*"descrizione" => "Cerca ".$pt['descrizione'],*/
                    "descrizione" => "Gestisci",
                    "class"       => "fa-search",
                    "header"      => "0",
                    "visible"     => "1",
                    "id_padre"    => "9999991" . $pt['id'],
                    "ordine"      => "1"
                ];
                $add_menu[] = [
                    "id"          => "9999993" . $pt['id'],
                    "risorsa"     => "posts",
                    "azione"      => "new/" . $pt['id'],
                    /*"descrizione" => "Nuovo ".$pt['descrizione'],*/
                    "descrizione" => "Inserisci",
                    "class"       => "fa-plus",
                    "header"      => "0",
                    "visible"     => "1",
                    "id_padre"    => "9999991" . $pt['id'],
                    "ordine"      => "2"
                ];
            }
        }
        if (!empty($add_menu)) {
            $dashboard = array_shift($menu);
            $new_menu = array_merge($add_menu, $menu);
            $new_menu = array_merge([$dashboard], $new_menu);
            return $this->createMenu($new_menu);
        } else {
            return $this->createMenu($menu);
        }
    }

    private function createMenu($menu, $id_padre = 0)
    {
        $m = [];

        foreach ($menu as $id => $item) {
            if ($item ['id_padre'] == $id_padre) {
                $item ['sub_menu'] = $this->createMenu($menu, $item ['id']);
                $m [] = $item;
            }
        }

        return $m;
    }
}