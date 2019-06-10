<?php
namespace apps\site\library;
use Phalcon\Tag;

class Translation extends Tag {

    protected static function t($string){
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

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this, 't'], $arguments);
    }
}

