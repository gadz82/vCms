<?php
/**
 * Created by PhpStorm.
 * User: Francesco
 * Date: 11/06/2019
 * Time: 12:23
 */

namespace apps\api\library;


class Translation
{
    public static function get($string){
        $translation = \FlatTranslations::findFirst([
            'columns' => 'translation',
            'conditions' => 'original_string = ?1 AND id_applicazione = ?2 AND attivo = 1',
            'bind' => [1 => $string, 2 => \apps\site\library\Cms::getIstance()->id_application],
            'cache' => [
                'key' => 'translationFor'.$string.\apps\site\library\Cms::getIstance()->id_application,
                'lifetime' => 360000
            ]
        ]);
        return $translation ? $translation->translation : $string;
    }
}