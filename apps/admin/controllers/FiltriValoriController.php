<?php

use apps\admin\forms\filtri_valori as FiltriValoriForms;

use Phalcon\Paginator\Adapter\Model as Paginator;

class FiltriValoriController extends ControllerBase {
		
	public function initialize(){

		$this->tag->setTitle('Valore Filtro');
		parent::initialize();
		
		$this->alert_messagge['notFound'] = 'Valore Filtro non trovato!';
		
		$this->alert_messagge['successCreate'] = 'Valore Filtro creato con successo!';
		$this->alert_messagge['failCreate'] = 'Errore creazione valore filtro!';
		
		$this->alert_messagge['successUpdate'] = 'Valore Filtro aggiornato con successo!';
		$this->alert_messagge['failUpdate'] = 'Errore aggiornamento valore filtro!';
		
		$this->alert_messagge['successDelete'] = 'Valore Filtro eliminato con successo!';
		$this->alert_messagge['failDelete'] = 'Errore eliminazione valore filtro!';
		
		$this->jqGrid_columns = array(
            array('label'=>'Filtro', 'name'=>'id_filtro'),
            array('label'=>'Valore Filtro', 'name'=>'valore'),
            array('label'=>'Chiave Valore', 'name'=>'key'),
            array('label'=>'Meta Title', 'name'=>'meta_title')
		);
		
	}
	
	public function indexAction(){
		parent::indexAction();
		
		$jqGrid_select_editoptions = array();
		
		$this->view->entityId =  str_replace('/', '_', $this->controllerName);
		$this->view->jqGrid = $this->jqGrid_init($this->controllerName, 'Valore Filtro', $this->jqGrid_columns, $jqGrid_select_editoptions);
		
		$form = new FiltriValoriForms\IndexForm();
		$this->view->form = $form;
		
		$this->assets->addJs('assets/admin/js/grid.js');
		
	}
	
	public function searchAction(){
						
		if($this->request->isPost() || $this->request->hasPost('export')){
			if($this->request->isAjax() || $this->request->hasPost('export')){
					
				if($this->request->hasPost('form_search')){
					$data = $this->request->getPost('form_search');
					parse_str($data,$search);
				}else{
					$search = $this->request->getPost();
				}
																
				$query = self::fromInput($this->di, 'FiltriValori', $search);
				$query->andWhere('FiltriValori.attivo = 1');

			
				$this->persistent->parameters = $query->getParams();
				$this->persistent->searchParams = $search;
		
				$parameters = $this->persistent->parameters;
				if(!is_array($parameters)) $parameters = array();
				
				//verifica ordinamento
				$sort = ($this->request->hasPost('sort') && !empty($this->request->getPost('sort'))) ? $this->request->getPost('sort') : 'id';
				$order = ($this->request->hasPost('order') && !empty($this->request->getPost('order'))) ? $this->request->getPost('order') : 'DESC';
				
				$parameters ['order'] = 'FiltriValori.' . $sort . ' ' . $order;
				$parameters['group'] = 'FiltriValori.id';
			
				//effettua la ricerca
				$controller_data = FiltriValori::find($parameters);
			
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
			
				$now = new DateTime(date('Y-m-d'));
				$paging = $paginator->getPaginate();

                foreach($paging->items as $item){
                    $item->id_filtro = $item->Filtri->titolo;
                }

				if($this->request->hasPost('export')) {
					//crea un file excel con il risultato della ricerca
					$this->jqGridExport($paging->items);
				} else {
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
        $this->view->form = new FiltriValoriForms\NewForm();
        $this->assets->addJs('assets/admin/js/filtri_valori/common.js');
	}
	
	public function createAction(){
        if (! $this->request->isPost ()) {
            return $this->dispatcher->forward(array(
                'controller' => $this->router->getControllerName(),
                'action' => 'index'
            ));
        }

        $param = $this->request->getPost ();

        $values = explode(',', $param['valore']);
        $nr = count($values);

        for($i = 0; $i < $nr; $i++){
            if(strpos($values[$i], ':') > -1){
                list($key, $valore) = explode(':', $values[$i], 2);
            } else {
                $key = self::slugify($values[$i]);
                $valore = $values[$i];
            }
            $params = [
                'id_filtro' => $param['id_filtro'],
                'valore' => $valore,
                'key' => $key,
                'data_creazione' =>  date ( 'Y-m-d H:i:s' )
            ];
            if(isset($param['id_filtro_valore_parent']) && !empty($param['id_filtro_valore_parent'])){
                $params['id_filtro_valore_parent'] = $param['id_filtro_valore_parent'];
            }

            $form = new FiltriValoriForms\NewForm();

            $filtri_valori = new FiltriValori();
            if (! $filtri_valori->save($params)) {
                $this->flash->error ( $filtri_valori->getMessages () );
                return $this->dispatcher->forward ( array (
                    'controller' => $this->router->getControllerName(),
                    'action' => 'new'
                ) );
            }

        }
        $this->flashSession->success ( $this->alert_messagge ['successCreate'] );
        $form->clear ();
        return $this->response->redirect ( $this->controllerName . '/index' );

	}

	public function editAction($id){
		
		$controller_data = FiltriValori::findFirstById($id);
		
		if(!$controller_data){
			$this->flashSession->error($this->alert_messagge['notFound']);
			return $this->dispatcher->forward(array('controller'=>$this->controllerName, 'action'=>'index'));
		}
				
		$form = new FiltriValoriForms\EditForm($controller_data);
				
		/*
		 * Verifica se la richiesa è in POST (richiesta update)
		 */
		if($this->request->isPost()){
				
			$params = $this->request->getPost();
			
			if($form->isValid($params)){

				$controller_data->assign($params);
			    if(!isset($params['id_filtro_valore_parent']) || is_null($controller_data->id_filtro_valore_parent) || empty($controller_data->id_filtro_valore_parent)){
			        $controller_data->id_filtro_valore_parent = null;
                }
                if(!isset($params['meta_title']) || is_null($controller_data->meta_title) || empty($controller_data->meta_title)){
                    $controller_data->meta_title = null;
                }
                if(!isset($params['meta_description']) || is_null($controller_data->meta_description) || empty($controller_data->meta_description)){
                    $controller_data->meta_description = null;
                }

				if($controller_data->save()){
					$this->flashSession->success($this->alert_messagge['successUpdate']);
					return $this->response->redirect($this->controllerName.'/index');
				}else{
					$message = array();
					foreach($controller_data->getMessages() as $message){
						$messages[] = $message;
					}
					$this->flash->error(implode(' | ',$messages));
				}

			}else{
				$message = array();
				foreach($form->getMessages() as $message){
					$messages[] = $message;
				}
				$this->flash->error(implode(' | ',$messages));
			}
				
		}
				
		$this->view->id = $id;
		$this->view->auth_user = $this->getDI()->getSession()->get('auth-identity');
		$this->view->form = $form;
		$this->view->controller_data = $controller_data;
        $this->view->id_filtro_valore_parent = $controller_data->id_filtro_valore_parent;
		$this->addLibraryAssets(array('jQueryValidation', 'dataTables'), $this->controllerName.'-edit');

		$this->assets->addJs('assets/admin/js/filtri_valori/edit.js');

	}
	
	public function saveAction(){
		
	}

	public function checkFiltroAction($id_filro){

        $has_parent = false;
        $has_children = false;
        $valori_filtro_collegato = $this->modelsManager->createBuilder()
            ->columns('fv.id, fv.valore, fc.titolo AS titolo_filtro')
            ->from('Filtri')
            ->innerJoin('Filtri', 'fc.id = Filtri.id_filtro_parent AND fc.attivo = 1', 'fc')
            ->innerJoin('FiltriValori', 'fv.id_filtro = fc.id AND fv.attivo = 1', 'fv')
            ->where('Filtri.id = '.$id_filro)
            ->andWhere('Filtri.attivo = 1')
            ->groupBy('fv.id')
            ->getQuery()->execute();


        if($valori_filtro_collegato->count() > 0){
            $has_parent = true;
        } else {
            $valori_filtro_collegato = $this->modelsManager->createBuilder()
                ->columns('fv.id, fv.valore, Filtri.titolo AS titolo_filtro')
                ->from('Filtri')
                ->innerJoin('FiltriValori', 'fv.id_filtro = Filtri.id AND fv.attivo = 1', 'fv')
                ->where('Filtri.id = '.$id_filro)
                ->andWhere('Filtri.attivo = 1')
                ->andWhere('fv.id_filtro_valore_parent IS NULL')
                ->groupBy('fv.id')
                ->getQuery()->execute();
            if($valori_filtro_collegato->count() > 0) {
                $has_children = true;
            }
        }
        $rs = [];
        if($has_parent || $has_children){
            $rs = $this->view->getRender('filtri_valori', 'filtriValoriSelect', [
                'valori' => $valori_filtro_collegato,
                'titolo_filtro_parent' => $valori_filtro_collegato[0]->titolo_filtro,
                'hasParent' => $has_parent
            ], function ($view){
                $view->setViewsDir("../apps/admin/views/partials/");
                $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
            });
        }

        $this->response->setJsonContent(array('data' => $rs, 'has_select' => ($has_parent || $has_children)));
        return $this->response;
    }

    public function getChildrenFilterValuesAction(){

        if($this->request->isAjax() && $this->request->isPost()){
            $params = $this->request->getPost();
            if(isset($params['id_filtri_valore'], $params['id_filtro'])){
                $inc = implode(',', $params['id_filtri_valore']);
                $fv = FiltriValori::find([
                    'conditions' => 'FiltriValori.id_filtro_valore_parent IN ('.$inc.') AND f.id_filtro_parent IS NOT NULL AND FiltriValori.attivo = 1',
                    'joins' => [
                        ['Filtri', 'f.id = FiltriValori.id_filtro AND f.attivo = 1', 'f', 'INNER']
                    ],
                    'group' => 'FiltriValori.id'
                ]);
                $res = [];
                if($fv->count() > 0){
                    foreach($fv as $filtroValore){
                        $key = $filtroValore->Filtri->TipologieFiltro->descrizione == 'Multiselect' ?
                            'filtri['.$filtroValore->Filtri->FiltriGroup->descrizione.']['.$filtroValore->id_filtro.'][]' :
                            'filtri['.$filtroValore->Filtri->FiltriGroup->descrizione.']['.$filtroValore->id_filtro.']';
                        if(!array_key_exists($key, $res)){
                            $res[$key] = [];
                        }
                        $res[$key][] = ['id' => $filtroValore->id, 'value' => $filtroValore->valore];
                    }
                } else {
                    $filtri = Filtri::find([
                        'conditions' => 'id_filtro_parent = '.$params['id_filtro'].' AND attivo = 1'
                    ]);
                    foreach($filtri as $filtro){
                        $key = $filtro->TipologieFiltro->descrizione == 'Multiselect' ?
                            'filtri['.$filtro->FiltriGroup->descrizione.']['.$filtro->id.'][]' :
                            'filtri['.$filtro->FiltriGroup->descrizione.']['.$filtro->id.']';
                        if(!array_key_exists($key, $res)){
                            $res[$key] = [];
                        }
                        $res[$key] = [];
                    }
                }

                $this->response->setJsonContent($res);
            }
        }
        return $this->response;

    }
	
	public function deleteAction($id){
	
		$controller_data = FiltriValori::findFirstById($id);
		
		if(!$controller_data){
			$this->flashSession->error($this->alert_messagge['notFound']);
			return $this->response->redirect($this->controllerName.'/index');
		}
		
		if(!$controller_data->delete()){
			foreach($controller_data->getMessages() as $message){
				$this->flashSession->error($message);
				return $this->response->redirect($this->controllerName.'/index');
			}
		}
	
		$this->flashSession->success($this->alert_messagge['successDelete']);
	
		return $this->response->redirect($this->controllerName.'/index');
		
	}
		
}
