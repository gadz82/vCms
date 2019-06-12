<?php

class IndexController extends ControllerBase
{

    public function initialize()
    {
        // Set the document title
        $this->tag->setTitle('Dashboard');
        parent::initialize();
    }

    public function indexAction()
    {
        parent::indexAction();
        $auth_user = $this->getDI()->getSession()->get('auth-identity');
        if ($auth_user ['id_tipologia_utente'] != 1 || $auth_user ['id_ruolo'] != 1) {
            $this->view->dashboard = 'dashboard';
        } else {
            $this->view->dashboard = 'dashboard_admin';
        }

        /*$this->view->richieste = FormRequests::find(
            [
                'conditions' => 'letto = 0 AND id_form = 2',
                'order' => 'data_creazione DESC'
            ]
        );*/
        $this->view->richieste = FormRequests::query()
            ->columns([
                'FormRequests.id AS id',
                'FormRequests.data_creazione AS data_creazione',
                'frf.input_value AS gruppo',
                'frfe.input_value AS email'
            ])
            ->innerJoin('Forms', 'f.id = FormRequests.id_form AND f.attivo = 1', 'f')
            ->innerJoin('FormFields', 'ff.id_form = f.id AND ff.name = "nome_gruppo" AND ff.id_tipologia_stato = 1 AND ff.attivo = 1', 'ff')
            ->innerJoin('FormRequestsFields', 'frf.id_form_request = FormRequests.id AND frf.id_form = f.id AND frf.id_form_field = ff.id AND frf.attivo = 1', 'frf')
            ->innerJoin('FormFields', 'ffe.id_form = f.id AND ffe.name = "email" AND ffe.id_tipologia_stato = 1 AND ff.attivo = 1', 'ffe')
            ->innerJoin('FormRequestsFields', 'frfe.id_form_request = FormRequests.id AND frfe.id_form = f.id AND frfe.id_form_field = ffe.id AND frfe.attivo = 1', 'frfe')
            ->where('FormRequests.id_form = 2 AND FormRequests.attivo = 1')
            ->orderBy('FormRequests.data_creazione DESC')
            ->execute();

        $this->view->posts = Posts::query()
            ->columns([
                'tp.descrizione',
                'tp.id',
                'tp.admin_icon',
                'COUNT(Posts.id) AS totale_post'
            ])
            ->innerJoin('TipologiePost', 'tp.id = Posts.id_tipologia_post AND tp.attivo = 1', 'tp')
            ->where('tp.admin_menu = 1 AND tp.attivo = 1')
            ->groupBy('tp.id')
            ->execute();

        $this->view->dashboard = 'dashboard_admin';

        $this->addLibraryAssets([
            'knob',
            'dataTables'
        ], 'index-index');
        $this->assets->addJs('assets/admin/js/index/index.js');
    }

    public function indexStatusAction()
    {

        $tipologiePosts = TipologiePost::find()->toArray();
        $option = Options::findFirstByOptionName('reindex_queue');
        $reindex = [];
        if ($option) {
            $reindex = json_decode($option->option_value, true);
        }

        $nr = count($tipologiePosts);
        for ($i = 0; $i < $nr; $i++) {
            $tipologiePosts[$i]['status'] = in_array($tipologiePosts[$i]['id'], $reindex) ? 'ko' : 'ok';
            $tipologiePosts[$i]['numero_post'] = Posts::count('id_tipologia_post = ' . $tipologiePosts[$i]['id']);
        }
        $this->assets->addJs('assets/admin/js/index/indexStatus.js');

        $this->view->post_types = $tipologiePosts;
    }

    public function rebuildIndexAction()
    {
        if ($this->request->isPost() && $this->request->isAjax()) {
            $params = $this->request->getPost();
            if (!isset($params['id_tipologia_post'])) $this->response->setStatusCode(500, 'Bad Request');
            $flatTablesManager = new \apps\admin\plugins\FlatTablesManagerPlugin();

            $postType = TipologiePost::findFirstById($params['id_tipologia_post']);
            if (!$postType) $this->response->setStatusCode(500, 'Tipologia Post Inesistente');

            $indexRs = $flatTablesManager->indexPostType($postType);
            if ($indexRs['success']) {
                $option = \Options::findFirstByOptionName('reindex_queue');
                if ($option) {
                    $post_types_to_reindex = json_decode($option->option_value, true);
                    $key = array_search($params['id_tipologia_post'], $post_types_to_reindex);
                    if ($key !== false) {
                        unset($post_types_to_reindex[$key]);
                    }
                    $option->option_value = json_encode(array_values($post_types_to_reindex));
                    $option->save();
                }
            }
            $this->response->setJsonContent([
                'success' => $indexRs
            ]);
            return $this->response;
        } else {
            $this->response->redirect('index/index');
        }
    }

}