<?php
use apps\admin\library\behaviors\Timestampable;
use Phalcon\Mvc\Model\Behavior\SoftDelete;
use apps\admin\library\behaviors\UserSession;

class BaseModel extends \Phalcon\Mvc\Model
{
    protected $isSoftDelete = false;

    /**
     * @param null $parameters
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function find($parameters = null)
    {
        $parameters = self::excludeSoftDelete($parameters);
        $parameters = self::filterCurrentApp($parameters);
        return parent::find($parameters);
    }

    /**
     * @param null $parameters
     * @return null|string
     */
    public static function excludeSoftDelete($parameters = null)
    {
        if (\Phalcon\Di::getDefault()->getSession()->get('disabled-soft-delete-clause')) {
            \Phalcon\Di::getDefault()->getSession()->remove('disabled-soft-delete-clause');
            return $parameters;
        }

        return self::addFilterToParameters($parameters, 'attivo', '1');
    }

    protected static function addFilterToParameters($parameters, $fieldName, $value)
    {
        if ($parameters === null) {
            $parameters = $fieldName . ' = ' . $value;
        } else if (is_array($parameters) === false && strpos($parameters, $fieldName) === false) {
            $parameters .= ' AND ' . $fieldName . ' = ' . $value;
        } else if (is_array($parameters) === true) {
            if (isset ($parameters [0]) === true) {
                if (is_array($parameters [0]) === true && isset ($parameters [0] ['conditions']) === true && strpos($parameters [0] ['conditions'], $fieldName) === false) {
                    $parameters [0] ['conditions'] .= ' AND ' . $fieldName . ' = ' . $value;
                } else if (is_array($parameters [0]) === false && strpos($parameters [0], $fieldName) === false) {
                    $parameters [0] .= ' AND ' . $fieldName . ' = ' . $value;
                }
            } else if (isset ($parameters ['conditions']) === true && strpos($parameters ['conditions'], $fieldName) === false) {
                $parameters ['conditions'] .= ' AND ' . $fieldName . ' = ' . $value;
            }
        }
        return $parameters;
    }

    public static function filterCurrentApp($parameters = null)
    {
        $session = \Phalcon\Di::getDefault()->getSession();

        if (
            $session->has('current_app') === false ||
            is_null($session->get('current_app')) ||
            !isset($session->get('current_app')['id'])
        ) {
            return $parameters;
        }
        $id_app = $session->get('current_app')['id'];
        $currentClass = get_called_class();

        if ($currentClass == 'Applicazioni') {
            return self::addFilterToParameters($parameters, 'id', $id_app);
        }

        $model = new $currentClass;
        $metaData = $model->getModelsMetaData();

        if ($metaData->hasAttribute($model, 'id_applicazione')) {
            return self::addFilterToParameters($parameters, 'id_applicazione', $id_app);
        } else {
            return $parameters;
        }
    }

    /**
     * @param null $parameters
     * @return \Phalcon\Mvc\Model
     */
    public static function findFirst($parameters = null)
    {
        $parameters = self::excludeSoftDelete($parameters);
        $parameters = self::filterCurrentApp($parameters);
        return parent::findFirst($parameters);
    }

    /**
     * @param null $parameters
     * @return mixed
     */
    public static function count($parameters = null)
    {
        $parameters = self::excludeSoftDelete($parameters);
        return parent::count($parameters);
    }

    /**
     * Bootstrap Model CMS
     */
    public function initialize()
    {
        $this->useDynamicUpdate(true);

        $this->skipAttributesOnCreate([
            'attivo'
        ]);
        $this->skipAttributesOnUpdate([
            'data_aggiornamento'
        ]);

        $this->addBehavior(new Timestampable());
        $this->addBehavior(new SoftDelete ([
            'field' => 'attivo',
            'value' => 0
        ]));
        $this->addBehavior(new UserSession());
        $this->setSchema(\Phalcon\Di::getDefault()->get('config')->database->dbname);
    }

    public function afterDelete()
    {
    }

}