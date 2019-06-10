<?php

class MediaController extends ControllerBase
{
    public function initialize(){
        parent::initialize();
        $this->response->setHeader('X-Robots-Tag', 'noindex');
    }

    public function renderAction($filename){
        $get = $this->request->getQuery();
        $size = isset($get['size']) ? $get['size'] : null;
        if(!$this->isUserLoggedIn){
            $this->flashSession->error('Effettua la login per visualizzare il file');
            $this->session->set('site_redirect_after_login', $this->currentUrl);
            return $this->response->redirect('/user');
        } else {
            $this->view->disable();
            $file = Files::findFirst([
                'conditions' => 'filename = :filename: AND attivo = 1',
                'bind'       => ['filename' => $filename],
                'cache' => [
                    "key" => "getMediaFiles_".$filename,
                    "lifetime" => 12000
                ]
            ]);
            $filePath = is_null($size) ? FILES_DIR.'reserved/'.$filename : FILES_DIR.'reserved/'.$size.'/'.$filename;
            if($file && file_exists($filePath)){
                //check permesso gruppo utente
                $fug = FilesUsersGroups::findFirst([
                    'conditions' => 'id_file = :id_file: AND id_user_group = :id_user_group: AND attivo = 1',
                    'bind'       => ['id_file' => $file->id, 'id_user_group' => $this->user['id_users_groups']],
                    'cache' => [
                        "key" => "getMediaFUG".$file->id,
                        "lifetime" => 12000
                    ]
                ]);

                if(!$fug){
                    return $this->response->redirect('/404', false, 403);
                }

                $this->response->setContentType($file->filetype);
                $this->response->setHeader('Content-Disposition', 'inline; filename='.$filename);
                $this->response->setExpires(new \DateTime('+1 month'));
                $this->response->setLastModified(new \DateTime($file->data_aggiornamento));

                readfile($filePath);
                $this->response->send();
                return;
            } else {
                return $this->response->redirect('/404', false, 404);
            }
        }
    }
}