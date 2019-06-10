<?php

use apps\admin\forms\posts as PostsForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class PostsController extends ControllerBase {

    private $id_tipologia_post;
    private $id_applicazione;

	public function initialize(){

		$this->tag->setTitle('Post');
		parent::initialize();
		$this->id_applicazione = !is_null($this->session->current_app) ? $this->session->current_app['id'] : 1;
		$this->alert_messagge['notFound'] = 'Post non trovato!';
		$this->alert_messagge['successCreate'] = 'Post creato con successo!';
		$this->alert_messagge['failCreate'] = 'Errore creazione post!';
		
		$this->alert_messagge['successUpdate'] = 'Post aggiornato con successo!';
		$this->alert_messagge['failUpdate'] = 'Errore aggiornamento post!';
		
		$this->alert_messagge['successDelete'] = 'Post eliminato con successo!';
		$this->alert_messagge['failDelete'] = 'Errore eliminazione post!';
		
		$this->jqGrid_columns = array(
			array('label' =>'Applicazione', 'name'=>'id_applicazione'),
			array('label' =>'Titolo', 'name'=>'titolo'),
			array('label' =>'Stato post', 'name'=>'id_tipologia_stato'),
			array('label' =>'Tipo post', 'name'=>'id_tipologia_post'),
			array('label' =>'Data Creazione', 'name'=>'data_creazione')
		);

	}

    public function indexAction(){
        /**
         * @TODO Inserire id_utente in tutti i models
         */

		parent::indexAction();

        $args = func_get_args();

        if(!empty($args) && isset($args[0])){
            $this->getDI()->getSession()->set('id_tipologia_post_current', $args[0]);
            $this->id_tipologia_post = $args[0];
        } else {
            $this->getDI()->getSession()->remove('id_tipologia_post_current');
        }

        if(!empty($args) && isset($args[1])){
            $this->getDI()->getSession()->set('id_applicazione', $args[1]);
            $this->id_applicazione = $args[1];
        } else {
            $this->getDI()->getSession()->remove('id_applicazione');
        }
        $this->view->controllerName = 'Contenuti';
        if(!empty($this->id_tipologia_post)){
            $tp = TipologiePost::findFirstById($this->id_tipologia_post);
            if($tp){
                $this->tag->setTitle($tp->descrizione);
                $this->view->controllerName = $tp->descrizione;
            }
        }

		$jqGrid_select_editoptions = array();

        $this->jqGrid_columns[] = ['label' =>'Apri', 'name'=>'slug'];
		$this->view->entityId = str_replace('/', '_', $this->controllerName);
		$this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Post', $this->jqGrid_columns, $jqGrid_select_editoptions);

		$form = new PostsForms\IndexForm();
		$this->view->form = $form;

		$this->assets->addJs('assets/admin/js/grid.js');
	}
	
	public function searchAction(){
						
		if($this->request->isPost() || $this->request->hasPost('export')){
			if($this->request->isAjax() || $this->request->hasPost('export')){
				if($this->request->hasPost('form_search')){
					$data = $this->request->getPost('form_search');
					parse_str($data,$search);
				} else {
					$search = $this->request->getPost();
				}
				$query = self::fromInput($this->di, 'Posts', $search);
				$query->andWhere('Posts.attivo = 1');

                $id_tipologia_post = $this->getDI()->getSession()->get('id_tipologia_post_current');

                if($id_tipologia_post && !isset($search['id_tipologia_post'])){
                    $query->andWhere('Posts.id_tipologia_post = '.$id_tipologia_post);
                }

                $id_applicazione = $this->getDI()->getSession()->get('current_app')['id'];

                if($id_applicazione){
                    $query->andWhere('Posts.id_applicazione = '.$id_applicazione);
                }

				$query->innerJoin('TipologieStatoPost', 'ts.id = Posts.id_tipologia_stato AND ts.attivo = 1', 'ts');
				$query->innerJoin('TipologiePost', 'tp.id = Posts.id_tipologia_post AND tp.attivo = 1', 'tp');

				$this->persistent->parameters = $query->getParams();
				$this->persistent->searchParams = $search;
		
				$parameters = $this->persistent->parameters;
				if(!is_array($parameters)) $parameters = array();
				
				//verifica ordinamento
				$sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
				$order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';

				$parameters ['order'] = 'Posts.' . $sort . ' ' . $order;
				$parameters['group'] = 'Posts.id';

				$controller_data = Posts::find($parameters);

				if($controller_data->count() == 0) return $this->response;
									
				//crea l'oggetto paginator
				if($this->request->hasPost('export')){
					$paginator = new Paginator(array('data'=>$controller_data, 'limit'=>65000, 'page'=>1));
				}else{
					$paginator = new Paginator(array(
						'data'	=> $controller_data,
						'limit'	=> ($this->request->hasPost('rows') && !empty($this->request->getPost('rows'))) ? $this->request->getPost('rows') : 20,
						'page'	=> ($this->request->hasPost('page') && !empty($this->request->getPost('page'))) ? $this->request->getPost('page') : 1,
					));
				}

				$paging = $paginator->getPaginate();
				foreach($paging->items as $item){
                    $item->id_tipologia_stato = $item->TipologieStatoPost->descrizione;
                    $item->id_tipologia_post = $item->TipologiePost->descrizione;
                    $item->id_applicazione = $item->Applicazioni->descrizione;
                    $item->slug = '<a class="btn btn-success btn-sm" target="_blank" href="/'.$item->TipologiePost->slug.'/'.$item->slug.'"><i class="fa fa-search"></i></a>';
                }
				
				if($this->request->hasPost('export')){
					//crea un file excel con il risultato della ricerca
                    if($id_tipologia_post && $id_tipologia_post == 3){
                        $this->jqGrid_columns[] = ['label' =>'Marca', 'name'=>'valore_marca'];
                        $this->jqGrid_columns[] = ['label' =>'Alimentazione', 'name'=>'valore_alimentazione'];
                        $this->jqGrid_columns[] = ['label' =>'Stato', 'name'=>'valore_stato_auto'];
                        $this->jqGrid_columns[] = ['label' =>'Prezzo', 'name'=>'meta_prezzo'];
                    }
					$this->jqGridExport($paging->items);
				}else{
					//crea l'array grid da passare a jqgrid
					$grid = array('records' => $paging->total_items,'page' => $paging->current,'total' => $paging->total_pages,'rows' => $paging->items);
						
					$this->response->setJsonContent($grid);
					return $this->response;
				}
			}
		}
		return $this->dispatcher->forward(array('controller'=>$this->controllerName, 'action'=>'index'));
	}
	
	public function newAction(){
        $args = func_get_args();

        $backlink = '/'.$this->controllerName.'/index';

        if(!empty($args) && isset($args[0])){
            $this->id_tipologia_post = $args[0];
            $backlink.= '/'.$this->id_tipologia_post;
        } else {
            $this->getDI()->getSession()->remove('id_tipologia_post_current');
        }

        if(!empty($args) && isset($args[1])){
            $this->id_applicazione = $args[1];
            $backlink.= '/'.$this->id_applicazione;
        } else {
            $this->getDI()->getSession()->remove('id_applicazione');
        }

        $this->getDI()->getSession()->set('id_tipologia_post_current', $this->id_tipologia_post);
        $this->getDI()->getSession()->set('id_applicazione', $this->id_applicazione);

		$form = new PostsForms\NewForm();
		$arr_form = ['Posts' => [],'meta' => [], 'filtri' => [], 'Tags' => [], 'userGroups' => []];

		foreach($form as $key=>$val){

			$name = $val->getName();
            $form_key = substr($name, 0, stripos($name,'['));
            if($form_key == 'meta' || $form_key == 'filtri'){
                list($m, $group, $meta) = explode('[', $name, 3);
                $arr_form[$form_key][str_replace(']', '', $group)][str_replace(']', '', $meta)] = $val;
            }
            if($form_key == 'Posts'){
                $arr_form['Posts'][] = $val;
            }
            if($form_key == 'Tags'){
                $arr_form['Tags'][] = $val;
            }
            if($form_key == 'userGroups'){
                $arr_form['userGroups'][] = $val;
            }
		}
        $sparams = $this->getDI()->getSession()->get('sparams');
        if($sparams){
            $posts = new Posts();
            $form->bind_custom($sparams, $posts);
            $this->getDI()->getSession()->remove('sparams');
        }
		$this->view->form = $form;
		$this->view->arr_form = $arr_form;
        $this->view->backlink = $backlink;

        $this->view->controllerName = 'Contenuti';
        if(!empty($this->id_tipologia_post)){
            $tp = TipologiePost::findFirstById($this->id_tipologia_post);
            if($tp){
                $this->tag->setTitle($tp->descrizione);
                $this->view->controllerName = $tp->descrizione;
            }
        }

		$this->addLibraryAssets(array('jQueryValidation', 'codemirror', 'jQueryFileUpload', 'bootstrapWysihtml5', 'dataTables', 'lazyload', 'jQueryPostMessage'), $this->controllerName.'-new');
		$this->assets->addJs('assets/admin/js/posts/common.js');
        $this->assets->addJs('assets/admin/js/bootstrap-select/bootstrap-select-ajax.js');
        $this->assets->addJs('assets/admin/js/posts/posts_files.js');
        $this->assets->addJs('assets/admin/js/posts/post-related.js');
        $this->assets->addJs('assets/admin/js/codemirror/codemirror.js');
        $this->assets->addCss('assets/admin/css/quick-modal/quickmodal.css');
	}
	
	public function createAction(){
        if (! $this->request->isPost ()) {
            return $this->dispatcher->forward ( array (
                'controller' => $this->router->getControllerName(),
                'action' => 'index'
            ) );
        }
        $params = $this->request->getPost ();

        if(empty($params['Posts']['data_inizio_pubblicazione'])) $params['Posts']['data_inizio_pubblicazione'] = date('Y-m-d');
        if(empty($params['Posts']['data_fine_pubblicazione'])) $params['Posts']['data_fine_pubblicazione'] = date('Y-m-d');
        if(empty($params['Posts']['excerpt'])) $params['Posts']['excerpt'] = ' ';

        $auth = $this->getDI ()->getSession ()->get( 'auth-identity' );
        $id_utente = $auth['id'];

        $sparams = $this->generateLinearParams($params);
        $form = new PostsForms\NewForm();
        $post = new Posts();
        $this->getDI()->getSession()->set('sparams', $sparams);
        if (!$form->isValid ( $sparams, $post )) {
            foreach ( $form->getMessages () as $message ) {
                $this->flash->error ( $message );
            }
            return $this->dispatcher->forward ( array (
                'controller' => $this->router->getControllerName(),
                'action' => 'new',
                'params' => ['id_tipologia_post' => $params['Posts']['id_tipologia_post']]
            ) );
        } else {

            $transaction = $this->beginTransaction();
            $post->assign($params['Posts']);
            $post->data_creazione = date('Y-m-d H:i:s');
            $post->slug = Posts::checkUniqueSlug(parent::slugify($params['Posts']['slug'], true), $params['Posts']['id_applicazione'], $params['Posts']['id_tipologia_post']);
            $post->id_utente = $id_utente;

            if (! $post->save ()) {

                $this->flash->error ( $post->getMessages () );
                $transaction->rollback();
                return $this->dispatcher->forward ( array (
                    'controller' => $this->router->getControllerName(),
                    'action' => 'new',
                    'params' => ['id_tipologia_post' => $params['Posts']['id_tipologia_post']]
                ) );
            }

            if(!empty($params['filtri'])){
                foreach($params['filtri'] as $gruppo_filtri => $valori){
                    foreach($valori as $id_filtro => $id_valore_filtro){
                        if(empty($id_valore_filtro)) continue;

                        if(!is_array($id_valore_filtro)){
                            $arr = [];
                            $arr[] = $id_valore_filtro;
                            $id_valore_filtro = $arr;
                        }

                        $nr = count($id_valore_filtro);
                        for($i = 0; $i < $nr; $i++){
                            $val = $id_valore_filtro[$i];
                            $post_filtri = new PostsFiltri();
                            $post_filtri->id_post = $post->id;
                            $post_filtri->id_filtro = $id_filtro;
                            $post_filtri->id_filtro_valore = $val;
                            $post_filtri->data_creazione = date('Y-m-d H:i:s');
                            $post_filtri->attivo = 1;
                            if (! $post_filtri->save ()) {
                                $this->flash->error ( $post_filtri->getMessages () );
                                $transaction->rollback();
                                return $this->dispatcher->forward ( array (
                                    'controller' => $this->router->getControllerName(),
                                    'action' => 'new',
                                    'params' => ['id_tipologia_post' => $params['Posts']['id_tipologia_post']]
                                ) );
                            }
                        }

                    }
                }
            }
            if(!empty($params['meta'])){

                foreach($params['meta'] as $meta_group => $valori){
                    foreach($valori as $id_meta => $valore){
                        $meta = Meta::findFirst(['conditions' => 'id = '.$id_meta]);
                        $tipologia_meta = $meta->TipologieMeta->descrizione;

                        $post_meta = new PostsMeta();
                        $post_meta->post_id = $post->id;
                        $post_meta->id_meta = $id_meta;
                        $post_meta->meta_key = $meta->key;
                        $post_meta->id_tipologia_stato = 1;
                        $post_meta->id_tipologia_post_meta = $meta->id_tipologia_meta;
                        $post_meta->data_creazione = date('Y-m-d H:i:s');
                        $post_meta->hidden = $meta->hidden;
                        $post_meta->attivo = 1;
                        $post_meta = $post_meta->setMetaValue($post_meta, $tipologia_meta, $valore);

                        if (! $post_meta->save ()) {
                            $this->flash->error ( $post_meta->getMessages () );
                            $transaction->rollback();
                            return $this->dispatcher->forward ( array (
                                'controller' => $this->router->getControllerName(),
                                'action' => 'new',
                                'params' => ['id_tipologia_post' => $params['Posts']['id_tipologia_post']]
                            ) );
                        }
                    }
                }
            }

            if(!empty($params['Tags']) && isset($params['Tags']['tags'])){
                if(!is_array($params['Tags']['tags']))$params['Tags']['tags'] = [$params['Tags']['tags']];
                $nr = count($params['Tags']['tags']);
                for($i = 0; $i < $nr; $i++){
                    $postTags = new PostsTags();
                    $postTags->id_post = $post->id;
                    $postTags->id_tag = $params['Tags']['tags'][$i];
                    $postTags->attivo = 1;
                    if (! $postTags->save ()) {
                        $this->flash->error ( $postTags->getMessages () );
                        $transaction->rollback();
                        return $this->dispatcher->forward ( array (
                            'controller' => $this->router->getControllerName(),
                            'action' => 'new',
                            'params' => ['id_tipologia_post' => $params['Posts']['id_tipologia_post']]
                        ) );
                    }
                }
            }

            if(!empty($params['userGroups']) && isset($params['userGroups']['groups'])){
                if(!is_array($params['userGroups']['groups']))$params['userGroups']['groups'] = [$params['userGroups']['groups']];
                $nr = count($params['userGroups']['groups']);
                for($i = 0; $i < $nr; $i++){
                    $postUg = new PostsUsersGroups();
                    $postUg->id_post = $post->id;
                    $postUg->id_user_group = $params['userGroups']['groups'][$i];
                    $postUg->attivo = 1;
                    if (! $postUg->save ()) {
                        $this->flash->error ( $postUg->getMessages () );
                        $transaction->rollback();
                        return $this->dispatcher->forward ( array (
                            'controller' => $this->router->getControllerName(),
                            'action' => 'new',
                            'params' => ['id_tipologia_post' => $params['Posts']['id_tipologia_post']]
                        ) );
                    }
                }
            }

            $this->flashSession->success ( $this->alert_messagge ['successCreate'] );
            $transaction->commit();
            $form->clear();
            $sparams = $this->getDI()->getSession()->get('sparams');
            if($sparams) $this->getDI()->getSession()->remove('sparams');
            $post->triggerSave(true);
            return $this->response->redirect ( $this->controllerName . '/index/'.$params['Posts']['id_tipologia_post']);
        }

	}

	private function generateLinearParams($params){
        $sparams = [];
        foreach($params as $key => $val){
            if($key == 'Posts' || $key == 'Tags' || $key == 'userGroups'){
                foreach($val as $k => $v){
                    $sparams[$key.'['.$k.']'] = $v;
                }
            } elseif($key == 'meta' || $key == 'filtri'){
                foreach($val as $gruppo => $colonne){
                    foreach($colonne as $kc => $col){
                        $sparams[$key.'['.$gruppo.']['.$kc.']'] = $col;
                    }
                }
            }
        }
        return $sparams;
    }

	public function cloneAction($id){
        $post = Posts::findFirstById($id);
        if(!$post){
            $this->flashSession->error($this->alert_messagge['notFound']);
            return $this->dispatcher->forward(array('controller'=>$this->controllerName, 'action'=>'index'));
        }
        $post_meta = $this->getPostMeta($id);
        $post_filter = $this->getPostFilters($id);
        $post_tags = $this->getPostTags($id);
        $post_users_groups = $this->getPostUserGroups($id);
        $arr_bind = array(
            'Posts' => $post->toArray(),
            'meta' => self::parseMetaToBind($post_meta),
            'filtri' => self::parseFiltersToBind($post_filter),
            'Tags' => self::parseTagsToBind($post_tags),
            'userGroups' => self::parseTagsToBind($post_users_groups)
        );

        $sparams = $this->generateLinearParams($arr_bind);
        $this->getDI()->getSession()->set('sparams', $sparams);
        return $this->response->redirect ( $this->controllerName . '/new/'.$post->id_tipologia_post.'/'.$post->id_applicazione);
    }

    private function getPostMeta($post_id){
        return $this->modelsManager->createBuilder()
            ->columns('
                PostsMeta.id_meta,
                PostsMeta.meta_value_int,
                PostsMeta.meta_value_decimal,
                PostsMeta.meta_value_varchar,
                PostsMeta.meta_value_text,
                PostsMeta.meta_value_datetime,
                PostsMeta.meta_value_files,
                tm.descrizione AS tipologia_meta,
                mg.descrizione AS descrizione_gruppo
            ')
            ->from('PostsMeta')
            ->innerJoin('Meta', 'm.id = PostsMeta.id_meta AND m.attivo = 1', 'm')
            ->innerJoin('TipologieMeta', 'tm.id = m.id_tipologia_meta AND tm.attivo = 1', 'tm')
            ->innerJoin('MetaGroup', 'mg.id = m.id_meta_group AND mg.attivo = 1', 'mg')
            ->where('PostsMeta.post_id = '.$post_id)
            ->andWhere('PostsMeta.attivo = 1 AND PostsMeta.id_tipologia_stato = 1')
            ->groupBy('PostsMeta.id')
            ->getQuery()->execute();
    }

    private function getPostFilters($post_id){
        return $this->modelsManager->createBuilder()
            ->columns('PostsFiltri.id_filtro, fg.descrizione AS descrizione_gruppo, PostsFiltri.id_filtro_valore')
            ->from('PostsFiltri')
            ->innerJoin('Filtri', 'f.id = PostsFiltri.id_filtro AND f.attivo = 1', 'f')
            ->innerJoin('FiltriGroup', 'fg.id = f.id_filtri_group AND fg.attivo = 1', 'fg')
            ->where('PostsFiltri.id_post = '.$post_id)
            ->andWhere('PostsFiltri.attivo = 1')
            ->groupBy('PostsFiltri.id')
            ->getQuery()->execute();
    }

    private function getPostTags($post_id){
        return $this->modelsManager->createBuilder()
            ->columns('PostsTags.id_tag, t.tag AS tag')
            ->from('PostsTags')
            ->innerJoin('Tags', 't.id = PostsTags.id_tag AND t.attivo = 1', 't')
            ->where('PostsTags.id_post = '.$post_id)
            ->andWhere('PostsTags.attivo = 1')
            ->groupBy('PostsTags.id')
            ->getQuery()->execute();
    }
    private function getPostUserGroups($post_id){
        return $this->modelsManager->createBuilder()
            ->columns('PostsUsersGroups.id_user_group, ug.titolo AS gt')
            ->from('PostsUsersGroups')
            ->innerJoin('UsersGroups', 'ug.id = PostsUsersGroups.id_user_group AND ug.attivo = 1', 'ug')
            ->where('PostsUsersGroups.id_post = '.$post_id)
            ->andWhere('PostsUsersGroups.attivo = 1')
            ->groupBy('PostsUsersGroups.id')
            ->getQuery()->execute();
    }

    private static function parseMetaToBind($posts_meta){
        $meta_bind = [];
        if($posts_meta){
            foreach($posts_meta as $meta){
                $meta_value = '';
                switch($meta->tipologia_meta){
                    case "Intero":
                        $meta_value = $meta->meta_value_int;
                        break;
                    case "Decimale":
                        $meta_value = $meta->meta_value_decimal;
                        break;
                    case "Stringa":
                        $meta_value = $meta->meta_value_varchar;
                        break;
                    case "Testo":
                        $meta_value = $meta->meta_value_text;
                        break;
                    case "Date/Time":
                        $meta_value = $meta->meta_value_datetime;
                        break;
                    case "Select":
                        $meta_value = $meta->meta_value_varchar;
                        break;
                    case "Checkbox":
                        $meta_value = $meta->meta_value_int;
                        break;
                    case "File":
                        $meta_value = $meta->meta_value_files;
                        break;
                    case "File Collection":
                        $meta_value = $meta->meta_value_varchar;
                        break;
                    case "Html":
                        $meta_value = $meta->meta_value_text;
                        break;
                }
                if(isset($meta_bind[$meta->descrizione_gruppo][$meta->id_meta])){
                    if(!is_array($meta_bind[$meta->descrizione_gruppo][$meta->id_meta])){
                        $old_f = $meta_bind[$meta->descrizione_gruppo][$meta->id_meta];
                        $meta_bind[$meta->descrizione_gruppo][$meta->id_meta] = [];
                        $meta_bind[$meta->descrizione_gruppo][$meta->id_meta][] = $old_f;
                        $meta_bind[$meta->descrizione_gruppo][$meta->id_meta][] = $meta_value;
                    } else {
                        $meta_bind[$meta->descrizione_gruppo][$meta->id_meta][] = $meta_value;
                    }
                } else {
                    $meta_bind[$meta->descrizione_gruppo][$meta->id_meta] = $meta_value;
                }
            }
        }

        return $meta_bind;
    }

	private function parseFiltersToBind($filtri){
        $filtri_bind = [];
        if($filtri){
            foreach($filtri as $filtro){
                if(isset($filtri_bind[$filtro->descrizione_gruppo][$filtro->id_filtro])){
                    if(!is_array($filtri_bind[$filtro->descrizione_gruppo][$filtro->id_filtro])){
                        $old_f = $filtri_bind[$filtro->descrizione_gruppo][$filtro->id_filtro];
                        $filtri_bind[$filtro->descrizione_gruppo][$filtro->id_filtro] = [];
                        $filtri_bind[$filtro->descrizione_gruppo][$filtro->id_filtro][] = $old_f;
                        $filtri_bind[$filtro->descrizione_gruppo][$filtro->id_filtro][] = $filtro->id_filtro_valore;
                    } else {
                        $filtri_bind[$filtro->descrizione_gruppo][$filtro->id_filtro][] = $filtro->id_filtro_valore;
                    }
                } else {
                    $filtri_bind[$filtro->descrizione_gruppo][$filtro->id_filtro] = $filtro->id_filtro_valore;
                }
            }
        }
        return $filtri_bind;
    }

    private function parseTagsToBind($tags){
        $tags_bind = ['tags' => []];
        if($tags){
            foreach($tags as $tag){
                $tags_bind['tags'][] = $tag->id_tag;
            }
        }
        return $tags_bind;
    }

	public function editAction($id){
        $backlink = '/'.$this->controllerName.'/index';

        if(!empty($args) && isset($args[0])){
            $this->id_tipologia_post = $args[0];
        } else {
            $this->getDI()->getSession()->remove('id_tipologia_post_current');
        }

        if(!empty($args) && isset($args[1])){
            $this->id_applicazione = $args[1];
            $backlink.= '/'.$this->id_applicazione;
        } else {
            $this->getDI()->getSession()->remove('id_applicazione');
        }
		$post = Posts::findFirstById($id);

        if($post->TipologiePost->admin_menu == '1'){
            $backlink.= '/'.$post->id_tipologia_post;
        }

        $this->getDI()->getSession()->set('id_applicazione', $post->id_applicazione);

        $old_slug = $post->slug;
		if(!$post){
			$this->flashSession->error($this->alert_messagge['notFound']);
			return $this->dispatcher->forward(array('controller'=>$this->controllerName, 'action'=>'index'));
		}

		$form = new PostsForms\EditForm($post);

        $post_meta = $this->getPostMeta($id);
        $post_filter = $this->getPostFilters($id);
        $post_tags = $this->getPostTags($id);
        $post_user_groups = $this->getPostUserGroups($id);
        $arr_bind = array(
            'Posts' => $post->toArray(),
            'meta' => self::parseMetaToBind($post_meta),
            'filtri' => self::parseFiltersToBind($post_filter),
            'Tags' => self::parseTagsToBind($post_tags),
            'userGroups' => self::parseUserGroupsToBind($post_user_groups)
        );
        $sparams = $this->generateLinearParams($arr_bind);
        $form->bind_custom($sparams, $post);

		/**
		 * Verifica se la richiesa è in POST (richiesta update)
		 */
		if($this->request->isPost()){

			$params = $this->request->getPost();
            $validateParams = $sparams;
            if(empty($validateParams['Posts[data_inizio_pubblicazione]'])) $validateParams['Posts[data_inizio_pubblicazione]'] = date('Y-m-d');
            if(empty($validateParams['Posts[data_fine_pubblicazione]'])) $validateParams['Posts[data_fine_pubblicazione]'] = date('Y-m-d');
            if(empty($validateParams['Posts[excerpt]'])) $validateParams['Posts[excerpt]'] = ' ';

			if ($form->isValid($validateParams)) {
			    $transaction = $this->beginTransaction();
				$post->assign($params['Posts']);

                if($post->slug !== $old_slug){
                    $post->slug = Posts::checkUniqueSlug(parent::slugify($params['Posts']['slug'], true), $params['Posts']['id_applicazione'], $params['Posts']['id_tipologia_post']);
                }

                if (! $post->save ()) {
                    $messages = array();
                    foreach($post->getMessages() as $message){
                        $messages[] = $message;
                    }
                    $this->flash->error(implode(' | ',$messages));
                }
                $this->handleEditMetaFilters($params, $post, $arr_bind, $transaction);

                if(!isset($params['Tags'], $params['Tags']['tags'])) {
                    $params['Tags']['tags'] = [];
                }
                if(empty($params['Tags']['tags'])) $params['Tags']['tags'] = [];
                $this->handleEditTags($params['Tags']['tags'], $post, $transaction);

                if(isset($params['userGroups'], $params['userGroups']['groups']) && !empty($params['userGroups']['groups'])){
                    $this->handleEditUserGroups($params['userGroups']['groups'], $post, $transaction);
                }

                $transaction->commit();
                $this->flashSession->success($this->alert_messagge['successUpdate']);
                $post->triggerSave(true);
                return $this->response->redirect($this->controllerName.'/edit/'.$id);

			} else {
				$message = array();
				foreach($form->getMessages() as $message){
					$messages[] = $message;
				}
				$this->flash->error(implode(' | ',$messages));
			}
		}
        $arr_form = ['Posts' => [],'meta' => [], 'filtri' => [], 'Tags' => [], 'userGroups' => []];

        foreach($form as $key=>$val){
            $name = $val->getName();
            $form_key = substr($name, 0, stripos($name,'['));
            if($form_key == 'meta' || $form_key == 'filtri'){
                list($m, $group, $meta) = explode('[', $name, 3);
                $arr_form[$form_key][str_replace(']', '', $group)][str_replace(']', '', $meta)] = $val;
            }
            if($form_key == 'Posts'){
                $arr_form['Posts'][] = $val;
            }
            if($form_key == 'Tags'){
                $arr_form['Tags'][] = $val;
            }
            if($form_key == 'userGroups'){
                $arr_form['userGroups'][] = $val;
            }
        }

		$this->view->id = $id;
		$this->view->auth_user = $this->getDI()->getSession()->get('auth-identity');
		$this->view->form = $form;
        $this->view->form = $form;
        $this->view->arr_form = $arr_form;
		$this->view->controller_data = $post;
        $this->view->backlink = $backlink;

        $this->view->controllerName = 'Contenuti';
        if(!empty($this->id_tipologia_post)){
            $tp = TipologiePost::findFirstById($this->id_tipologia_post);
            if($tp){
                $this->tag->setTitle($tp->descrizione);
                $this->view->controllerName = $tp->descrizione;
            }
        }

		$this->addLibraryAssets(array('jQueryValidation', 'jQueryFileUpload', 'codemirror', 'bootstrapWysihtml5', 'dataTables', 'lazyload', 'jQueryPostMessage'), $this->controllerName.'-edit');
		$this->assets->addJs('assets/admin/js/posts/edit.js');
        $this->assets->addJs('assets/admin/js/bootstrap-select/bootstrap-select-ajax.js');
        $this->assets->addJs('assets/admin/js/posts/posts_files.js');
        $this->assets->addJs('assets/admin/js/posts/post-related.js');
        $this->assets->addJs('assets/admin/js/codemirror/codemirror.js');
        $this->assets->addCss('assets/admin/css/quick-modal/quickmodal.css');
	}

    private function parseUserGroupsToBind($ugs){
        $ugs_bind = ['groups' => []];
        if($ugs){
            foreach($ugs as $ug){
                $ugs_bind['groups'][] = $ug->id_user_group;
            }
        }
        return $ugs_bind;
    }

    private function handleEditMetaFilters($params, Posts $post, $arr_bind, \Phalcon\Mvc\Model\Transaction $transaction){
        if(!empty($params['filtri'])){
            $postFilters = PostsFiltri::find([
                'conditions' => 'id_post = :id_post: AND attivo IN(0,1)',
                'bind' => array (
                    'id_post' => $post->id
                )
            ]);
            $filtri_request = $params['filtri'];
            foreach($postFilters as $postFilter){
                $gruppo_filtri = $postFilter->Filtri->FiltriGroup->descrizione;
                if(!array_key_exists($gruppo_filtri, $filtri_request)){
                    $postFilter->delete();
                    unset($filtri_request[$gruppo_filtri]);
                }
                if(array_key_exists($gruppo_filtri, $filtri_request) && !array_key_exists($postFilter->id_filtro, $filtri_request[$gruppo_filtri])){
                    $postFilter->delete();
                    unset($filtri_request[$gruppo_filtri][$postFilter->id_filtro]);
                }
                if(array_key_exists($postFilter->id_filtro, $filtri_request[$gruppo_filtri])){
                    if(is_array($filtri_request[$gruppo_filtri][$postFilter->id_filtro])){
                        $pos = array_search($postFilter->id_filtro_valore, $filtri_request[$gruppo_filtri][$postFilter->id_filtro]);
                        if($pos !== false){
                            if($postFilter->attivo == '0'){
                                $postFilter->attivo = 1;
                                $postFilter->save();
                            }
                            unset($filtri_request[$gruppo_filtri][$postFilter->id_filtro][$pos]);
                        } else {
                            $postFilter->delete();
                        }
                    } else {
                        if($postFilter->id_filtro_valore == $filtri_request[$gruppo_filtri][$postFilter->id_filtro]){
                            if($postFilter->attivo == '0'){
                                $postFilter->attivo = 1;
                                $postFilter->save();
                            }
                            unset($filtri_request[$gruppo_filtri][$postFilter->id_filtro]);
                        } else {
                            $postFilter->delete();
                        }
                    }
                }
            }
            if(!empty($filtri_request)){
                foreach($filtri_request as $gruppo_filtri => $valori){
                    foreach($valori as $id_filtro => $id_valore_filtro){
                        if(empty($id_valore_filtro)) continue;
                        if(!is_array($id_valore_filtro)){
                            $arr = [];
                            $arr[] = $id_valore_filtro;
                            $id_valore_filtro = $arr;
                        }
                        $id_valore_filtro = array_values($id_valore_filtro);

                        $nr = count($id_valore_filtro);
                        for($i = 0; $i < $nr; $i++){
                            $val = $id_valore_filtro[$i];
                            $post_filtri = new PostsFiltri();
                            $post_filtri->id_post = $post->id;
                            $post_filtri->id_filtro = $id_filtro;
                            $post_filtri->id_filtro_valore = $val;
                            $post_filtri->data_creazione = date('Y-m-d H:i:s');
                            $post_filtri->attivo = 1;
                            if (! $post_filtri->save ()) {
                                $this->flash->error ( $post_filtri->getMessages () );
                                $transaction->rollback();
                                return $this->dispatcher->forward ( array (
                                    'controller' => $this->router->getControllerName(),
                                    'action' => 'new',
                                    'params' => ['id_tipologia_post' => $params['Posts']['id_tipologia_post']]
                                ) );
                            }
                        }
                    }
                }
            }
        }

        if(!empty($params['meta'])){

            foreach($params['meta'] as $meta_group => $valori){

                //$valori = array_filter($valori);

                foreach($valori as $id_meta => $valore){
                    if(!array_key_exists($meta_group, $arr_bind['meta'])){
                        $meta = Meta::findFirst(['conditions' => 'id = '.$id_meta]);
                        $tipologia_meta = $meta->TipologieMeta->descrizione;
                        $post_meta = new PostsMeta();
                        $post_meta->post_id = $post->id;
                        $post_meta->id_meta = $id_meta;
                        $post_meta->meta_key = $meta->key;
                        $post_meta->id_tipologia_stato = 1;
                        $post_meta->id_tipologia_post_meta = $meta->id_tipologia_meta;
                        $post_meta->data_creazione = date('Y-m-d H:i:s');
                        $post_meta->hidden = $meta->hidden;
                        $post_meta->attivo = 1;
                        $post_meta = $post_meta->setMetaValue($post_meta, $tipologia_meta, $valore);

                        if (! $post_meta->save ()) {
                            $this->flash->error ( $post_meta->getMessages () );
                            $transaction->rollback();
                            return $this->dispatcher->forward ( array (
                                'controller' => $this->router->getControllerName(),
                                'action' => 'new',
                                'params' => ['id_tipologia_post' => $params['Posts']['id_tipologia_post']]
                            ) );
                        }
                    }elseif(!array_key_exists($id_meta, $arr_bind['meta'][$meta_group])){
                        $postMeta = PostsMeta::findFirst([
                            'conditions' => 'post_id = :id_post: AND id_meta = :id_meta: AND ( attivo = 0 OR id_tipologia_stato = 2)',
                            'bind' => array (
                                'id_post' => $post->id,
                                'id_meta' => $id_meta
                            )
                        ]);

                        if($postMeta){
                            if(!empty($valore)){
                                $postMeta = $postMeta->setMetaValue($postMeta, $postMeta->TipologieMeta->descrizione, $valore);
                                $postMeta->id_tipologia_stato = 1;
                                $postMeta->attivo = 1;
                            } else {
                                $postMeta = $postMeta->setMetaValue($postMeta, $postMeta->TipologieMeta->descrizione, null);
                            }
                            print_r($postMeta->toArray());
                            if (! $postMeta->save ()) {
                                $this->flash->error ( $postMeta->getMessages () );
                                $transaction->rollback();
                                return $this->dispatcher->forward ( array (
                                    'controller' => $this->router->getControllerName(),
                                    'action' => 'new',
                                    'params' => ['id_tipologia_post' => $params['Posts']['id_tipologia_post']]
                                ));
                            }
                        } else {
                            if(empty($valore)) continue;

                            $meta = Meta::findFirst(['conditions' => 'id = '.$id_meta]);
                            $tipologia_meta = $meta->TipologieMeta->descrizione;
                            $post_meta = new PostsMeta();
                            $post_meta->post_id = $post->id;
                            $post_meta->id_meta = $id_meta;
                            $post_meta->meta_key = $meta->key;
                            $post_meta->id_tipologia_stato = 1;
                            $post_meta->id_tipologia_post_meta = $meta->id_tipologia_meta;
                            $post_meta->data_creazione = date('Y-m-d H:i:s');
                            $post_meta->hidden = $meta->hidden;
                            $post_meta->attivo = 1;
                            $post_meta = $post_meta->setMetaValue($post_meta, $tipologia_meta, $valore);

                            if (! $post_meta->save ()) {
                                $this->flash->error ( $post_meta->getMessages () );
                                $transaction->rollback();
                                return $this->dispatcher->forward ( array (
                                    'controller' => $this->router->getControllerName(),
                                    'action' => 'new',
                                    'params' => ['id_tipologia_post' => $params['Posts']['id_tipologia_post']]
                                ) );
                            }
                        }
                    } else {
                        /**
                         * Valore già settato, recupero l'oggetto e in base al valore effettuo l'update
                         */
                        $postMeta = PostsMeta::findFirst([
                            'conditions' => 'post_id = :id_post: AND id_meta = :id_meta: AND attivo = 1 AND id_tipologia_stato = 1',
                            'bind' => array (
                                'id_post' => $post->id,
                                'id_meta' => $id_meta
                            )
                        ]);

                        if(!empty($valore)){
                            $postMeta = $postMeta->setMetaValue($postMeta, $postMeta->TipologieMeta->descrizione, $valore);
                        } else {
                            $postMeta = $postMeta->setMetaValue($postMeta, $postMeta->TipologieMeta->descrizione, '');
                        }

                        if (! $postMeta->save ()) {
                            $this->flash->error ( $postMeta->getMessages () );
                            $transaction->rollback();
                            return $this->dispatcher->forward ( array (
                                'controller' => $this->router->getControllerName(),
                                'action' => 'new',
                                'params' => ['id_tipologia_post' => $params['Posts']['id_tipologia_post']]
                            ) );
                        }
                    }
                }
            }
        }
    }

    private function handleEditTags($tags, Posts $post, \Phalcon\Mvc\Model\Transaction $transaction){

        $postTags = PostsTags::find([
            'conditions' => 'id_post = :id_post: AND attivo IN(0,1)',
            'bind' => array (
                'id_post' => $post->id
            )
        ]);

        foreach($postTags as $postTag){

            if(!in_array($postTag->id, $tags)){
                $postTag->delete();
            } else {
                if(is_array($tags)){
                    $pos = array_search($postTag->id_tag, $tags);
                    if($pos !== false){
                        if($postTag->attivo == '0'){
                            $postTag->attivo = 1;
                            $postTag->save();
                        }
                        unset($tags[$pos]);
                    } else {
                        $postTag->delete();
                    }
                } else {
                    if($postTag->id_tag == $tags){
                        if($postTag->attivo == '0'){
                            $postTag->attivo = 1;
                            $postTag->save();
                        }
                        unset($tags);
                    } else {
                        $postTag->delete();
                    }
                }
            }
        }
        $tags = array_filter($tags);
        if(isset($tags) && !empty($tags)){

            if(!is_array($tags)){
                $arr = [];
                $arr[] = $tags;
                $tags = $arr;
            }

            $nr = count($tags);
            for($i = 0; $i < $nr; $i++){
                $post_tags = new PostsTags();
                $post_tags->id_post = $post->id;
                $post_tags->id_tag = $tags[$i];
                $post_tags->data_creazione = date('Y-m-d H:i:s');
                $post_tags->attivo = 1;
                if (! $post_tags->save ()) {
                    $this->flash->error ( $post_tags->getMessages () );
                    $transaction->rollback();
                    return $this->dispatcher->forward ( array (
                        'controller' => $this->router->getControllerName(),
                        'action' => 'new',
                        'params' => ['id_tipologia_post' => $post->id_tipologia_post]
                    ) );
                }
            }
        }
    }

    private function handleEditUserGroups($ugs, Posts $post, \Phalcon\Mvc\Model\Transaction $transaction){
        $postUsersGroups = PostsUsersGroups::find([
            'conditions' => 'id_post = :id_post: AND attivo IN(0,1)',
            'bind' => array (
                'id_post' => $post->id
            )
        ]);

        foreach($postUsersGroups as $postUserGroup){

            if(!in_array($postUserGroup->id_user_group, $ugs)){
                $postUserGroup->delete();
            } else {
               /* print_r($ugs);
               /* print_r($ugs);
                print_r($postUserGroup->id_user_group);
                var_dump(in_array($postUserGroup->id_user_group, $ugs));
                echo '<br>';
                echo array_search($postUserGroup->id_user_group, $ugs);
                exit();*/

                if(is_array($ugs)){
                    $pos = array_search($postUserGroup->id_user_group, $ugs);

                    if($pos !== false){
                        if($postUserGroup->attivo == '0'){
                            $postUserGroup->attivo = 1;
                            $postUserGroup->save();
                        }
                        unset($ugs[$pos]);
                    } else {
                        $postUserGroup->delete();
                    }
                } else {
                    if($postUserGroup->id_user_group == $ugs){
                        if($postUserGroup->attivo == '0'){
                            $postUserGroup->attivo = 1;
                            $postUserGroup->save();
                        }
                        unset($ugs);
                    } else {
                        $postUserGroup->delete();
                    }
                }
            }
        }
        $ugs = array_filter($ugs);
        if(isset($ugs) && !empty($ugs)){

            if(!is_array($ugs)){
                $arr = [];
                $arr[] = $ugs;
                $ugs = $arr;
            }

            $nr = count($ugs);
            for($i = 0; $i < $nr; $i++){
                $post_ug = new PostsUsersGroups();
                $post_ug->id_post = $post->id;
                $post_ug->id_user_group = $ugs[$i];
                $post_ug->data_creazione = date('Y-m-d H:i:s');
                $post_ug->attivo = 1;
                if (! $post_ug->save ()) {
                    $this->flash->error ( $post_ug->getMessages () );
                    $transaction->rollback();
                    return $this->dispatcher->forward ( array (
                        'controller' => $this->router->getControllerName(),
                        'action' => 'new',
                        'params' => ['id_tipologia_post' => $post->id_tipologia_post]
                    ) );
                }
            }
        }
    }

	public function saveAction(){
		
	}
	
	public function deleteAction($id){
	
		$controller_data = Posts::findFirstById($id);
		if(!$controller_data){
			$this->flashSession->error($this->alert_messagge['notFound']);
			return $this->response->redirect($this->controllerName.'/index');
		}
		
		if(!$controller_data->delete()){
			foreach($controller_data->getMessages() as $message){
				$this->flashSession->error($message);
				return $this->response->redirect($this->controllerName.'/index');
			}
		} else {
            $controller_data->triggerSave(true);
        }
	
		$this->flashSession->success($this->alert_messagge['successDelete']);
	
		return $this->response->redirect($this->controllerName.'/index/'.$controller_data->id_tipologia_post.'/'.$controller_data->id_applicazione);
		
	}
		
}