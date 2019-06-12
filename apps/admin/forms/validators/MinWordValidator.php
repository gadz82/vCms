<?php

namespace app\forms\validators;

use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;

class MinWordValidator extends Validator implements ValidatorInterface
{
    public function validate(\Phalcon\Validation $validation, $attribute)
    {
        $value = $validation->getValue($attribute);

        $min_words = $this->getOption('min', 1000);
        if (str_word_count($value) < $min_words) {
            $validation->appendMessage(new Message ('[' . $attribute . '] Il campo deve contenere almeno ' . $min_words . ' parole', $attribute, 'MinWorld'));
            return false;
        }

        return true;
    }
}