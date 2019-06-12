<?php
namespace apps\admin\library\validation;

use Phalcon\Validation\Message;

/**
 * Created by PhpStorm.
 * User: Francesco
 * Date: 06/06/2019
 * Time: 10:46
 */
class JsonValidation extends \Phalcon\Validation\Validator
{

    /**
     * @param \Phalcon\Validation $validator
     * @param string $attribute
     * @return bool
     */
    public function validate(\Phalcon\Validation $validator, $attribute)
    {
        $value = $validator->getValue($attribute);
        if (!self::isJson($value)) {
            $validator->appendMessage(
                new Message('Formato Json campo Params non corretto', $attribute, 'params')
            );

            return false;
        }

        return true;
    }

    /**
     * @param $string
     * @return bool
     */
    protected static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}