<?php
/**
 * Created by PhpStorm.
 * User: Francesco
 * Date: 18/08/17
 * Time: 09:24
 */

namespace apps\admin\plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;


class AppSyncPlugin extends Plugin
{

    public function triggerEditSingleEntity(Event $event, \Posts $post)
    {
        $option = \Options::findFirst([
            'conditions' => 'option_name = "app_md5" AND attivo = 1',
            'cache'      => [
                'key'      => 'app_option_md5',
                'lifetime' => 1800
            ]
        ]);
        if (!$option) {
            $option = new \Options();
            $option->option_name = 'app_md5';
            $option->option_value = $post->data_aggiornamento;
            $option->save();
        } else {
            $option->option_value = $post->data_aggiornamento;
            $option->save();
        }
    }

    public function triggerDeleteSingleEntity(Event $event, \Posts $post)
    {
        $option = \Options::findFirst([
            'conditions' => 'option_name = "app_md5" AND attivo = 1',
            'cache'      => [
                'key'      => 'app_option_md5',
                'lifetime' => 1800
            ]
        ]);
        if (!$option) {
            $option = new \Options();
            $option->option_name = 'app_md5';
            $option->option_value = $post->data_aggiornamento;
            $option->save();
        } else {
            $option->option_value = $post->data_aggiornamento;
            $option->save();
        }
    }
}