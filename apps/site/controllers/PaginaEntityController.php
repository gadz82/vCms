<?php
/**
 * Created by PhpStorm.
 * User: Francesco
 * Date: 31/05/2019
 * Time: 16:27
 */
class PaginaEntityController extends EntityController{

    public function readAction(){
        $routeUri = $this->getDi()->get('router')->getRewriteUri();
        $post_type = $this->dispatcher->getParam('post_type_slug');
        $slug = $this->dispatcher->getParam('post_slug');
        $appUri = \apps\site\library\Cms::getIstance()->getApplicationUrl(null, true);

        if($slug == 'index') return $this->response->redirect($appUri, false, 301);

        if($post_type == 'pagina' && $routeUri == $appUri.$post_type.DIRECTORY_SEPARATOR.$slug){
            return $this->response->redirect($appUri.$slug, false, 301);
        }

        parent::readAction();
    }

}