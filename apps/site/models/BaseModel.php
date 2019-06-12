<?php

class BaseModel extends \Phalcon\Mvc\Model
{

    protected $isSoftDelete = false;

    public static function find($parameters = null)
    {
        $parameters = self::excludeSoftDelete($parameters);
        return parent::find($parameters);
    }

    public static function excludeSoftDelete($parameters = null)
    {
        if ($parameters === null) {
            $parameters = 'attivo = 1';
        } else if (is_array($parameters) === false && strpos($parameters, 'attivo') === false) {
            $parameters .= ' AND attivo = 1';
        } else if (is_array($parameters) === true) {
            if (isset ($parameters [0]) === true) {
                if (is_array($parameters [0]) === true && isset ($parameters [0] ['conditions']) === true && strpos($parameters [0] ['conditions'], 'attivo') === false) {
                    $parameters [0] ['conditions'] .= ' AND attivo = 1';
                } else if (is_array($parameters [0]) === false && strpos($parameters [0], 'attivo') === false) {
                    $parameters [0] .= ' AND attivo = 1';
                }
            } else if (isset ($parameters ['conditions']) === true && strpos($parameters ['conditions'], 'attivo') === false) {
                $parameters ['conditions'] .= ' AND attivo = 1';
            }
        }

        return $parameters;
    }

    public static function findFirst($parameters = null)
    {
        $parameters = self::excludeSoftDelete($parameters);
        return parent::findFirst($parameters);
    }

    public static function count($parameters = null)
    {
        $parameters = self::excludeSoftDelete($parameters);
        return parent::count($parameters);
    }

    public function initialize()
    {
        $this->setSchema(\Phalcon\Di::getDefault()->get('config')->database->dbname);
    }

}