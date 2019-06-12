<?php

namespace app\forms\validators;

use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;

class MinLengthValidator extends Validator implements ValidatorInterface
{
    public function validate(\Phalcon\Validation $validation, $attribute)
    {
        $value = $validation->getValue($attribute);

        $min_length = $this->getOption('min', 1000);
        if (!is_string($value) || strlen($value) < $min_length) {
            $validation->appendMessage(new Message ('[' . $attribute . '] Contenuto campo troppo corto', $attribute, 'MinLength'));
            return false;
        }

        return true;
    }
}