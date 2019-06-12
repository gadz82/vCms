<?php
use apps\admin\forms\session\LoginForm;

class SessionController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Login');

        parent::initialize();
    }

    public function indexAction()
    {
        $auth_user = $this->getDI()->getSession()->get('auth-identity');
        if ($this->di->getAuth()->hasIdentity()) {
            return $this->response->redirect('admin/index');
        } else {
            return $this->dispatcher->forward([
                'action' => 'login'
            ]);
        }
    }

    public function loginAction()
    {
        $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
        $form = new LoginForm ();

        try {

            if (!$this->request->isPost()) {
                if ($this->di->getAuth()->hasRemember()) {
                    return $this->di->getAuth()->login_remember();
                }
                if ($this->di->getAuth()->hasIdentity()) {
                    $ref = $this->session->get('redirect_after_login', 'admin/index');
                    $this->session->remove('redirect_after_login');
                    return $this->response->redirect($ref);
                }
            } else {
                if ($form->isValid($this->request->getPost())) {
                    $credential = [
                        'username' => $this->request->getPost('username'),
                        'password' => $this->request->getPost('password'),
                        'remember' => $this->request->getPost('remember')
                    ];
                    $this->di->getAuth()->login($credential);
                }
            }
        } catch (Exception $e) {
            $this->flashSession->error($e->getMessage());
            // $this->flashSession->error($this->security->hash($this->request->getPost('password')));
        }

        $this->view->form = $form;
    }

    public function logoutAction()
    {
        $this->di->getAuth()->remove();
        return $this->response->redirect('admin/session/login');
    }

    public function destroyAction()
    {
        apcu_clear_cache();
        apcu_delete('cmsio-acl');
        apcu_delete('cmsio-metadata');

        unlink(APP_DIR . '/cache/acl/data.txt');

        $arr_dir = [
            APP_DIR . '/cache/',
            APP_DIR . '/../site/cache/',
            BASE_DIR . '/../public/assets/admin/css/min/',
            BASE_DIR . '/../public/assets/admin/js/min/',
            BASE_DIR . '/../public/assets/site/css/min/',
            BASE_DIR . '/../public/assets/site/js/min/',
        ];

        $count = count($arr_dir);
        for ($i = 0; $i < $count; $i++) {
            if (!file_exists($arr_dir[$i])) {
                mkdir($arr_dir[$i], 0755);
            }
            foreach (new DirectoryIterator ($arr_dir [$i]) as $fileinfo) {
                if ($fileinfo->isDot())
                    continue;
                if (!$fileinfo->isFile())
                    continue;

                $filename = $fileinfo->getFilename();
                if (strlen(strstr($filename, '.', true)) < 1)
                    continue;

                unlink($fileinfo->getPathname());
            }
        }

        $css_expiration_token = \Options::findFirst([
            'conditions' => 'option_name = "version_number_css"'
        ]);
        if ($css_expiration_token) {
            $css_expiration_token->option_value = uniqid(time(), true);
            $css_expiration_token->save();
        }

        $js_expiration_token = \Options::findFirst([
            'conditions' => 'option_name = "version_number_js"'
        ]);
        if ($js_expiration_token) {
            $js_expiration_token->option_value = uniqid(time(), true);
            $js_expiration_token->save();
        }

        $assets_collections_expiration_token = \Options::findFirst([
            'conditions' => 'option_name = "version_number_assets_collections"'
        ]);
        if ($assets_collections_expiration_token) {
            $assets_collections_expiration_token->option_value = uniqid(time(), true);
            $assets_collections_expiration_token->save();
        }

        //$this->session->destroy ();

        return $this->response->redirect($this->request->getHTTPReferer());
    }

    public function toggleDebugAction()
    {
        if ($this->session->has('debug')) {
            $this->session->remove('debug');
        } else {
            $this->session->set('debug', true);
        }

        return $this->response->redirect('admin/index');
    }
}