<?php

namespace app\forms\validators;

use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;

class YearValidator extends Validator implements ValidatorInterface
{
    public function validate(\Phalcon\Validation $validation, $attribute)
    {
        $value = $validation->getValue($attribute);

        if ($value < 1900 || $value >= ((new \DateTime())->format('Y')) - 20) {
            $validation->appendMessage(new Message ('[' . $attribute . '] Valore anno non corretto o troppo recente', $attribute, 'Presence'));
            return false;
        }
        return true;

    }
}