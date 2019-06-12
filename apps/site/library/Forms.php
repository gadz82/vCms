<?php

namespace apps\site\library;

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Email;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;

/**
 * Class Forms
 * @package apps\site\library
 */
class Forms
{

    /**
     * @param $form_key
     * @param $id_post
     * @param Tags $tags Istanza corrente della classe Tags
     * @return array|bool
     */
    public static function getForm($form_key, $id_post, Tags $tags)
    {
        $formFields = \FormFields::query()
            ->innerJoin('Forms', 'Forms.id = FormFields.id_form AND Forms.attivo = 1')
            ->where('FormFields.attivo = 1 AND Forms.key = ?1 AND Forms.id_applicazione = ?2')
            ->bind([
                1 => $form_key,
                2 => Cms::getIstance()->id_application
            ])
            ->groupBy('FormFields.id, Forms.id')
            ->orderBy('FormFields.ordine')
            ->cache([
                "key"      => "getFormsFieldsByKey" . $form_key,
                "lifetime" => 12400
            ])->execute();

        if (!$formFields) return false;

        $form = new Form();

        foreach ($formFields as $field) {
            if ($field->obbligatorio) {
                if (!empty($field->placeholder)) $field->placeholder = $field->placeholder . ' *';
                $field->label = $field->label . ' *';
            }
            switch ($field->id_tipologia_form_fields) {
                case 1: //testo
                case 3: //telefono
                    $input = new Text($field->name, ['class' => 'form-control form-item']);
                    break;
                case 2: //email
                    $input = new Email($field->name, ['class' => 'form-control form-item']);
                    break;
                case 4: //numero
                    $input = new Numeric($field->name, ['class' => 'form-control form-item']);
                    break;
                case 5: //Textarea
                    $input = new TextArea($field->name, ['class' => 'form-control form-item h-150 m-b-lg-10']);
                    break;
                case 6: //Select
                case 8: //Multi Select
                    /*$tags->injectCssFromDi('/assets/site/css/bootstrap-select/bootstrap-select.min.css');
                    $tags->injectJsFromDi(['/assets/site/js/bootstrap-select/bootstrap-select.min.js', '/assets/site/js/bootstrap-select/i18n/defaults-it_IT.min.js']);*/
                    $values = explode(',', $field->value);
                    $options = [];
                    if (!empty($values)) {
                        foreach ($values as $val) {
                            if (strpos($val, ':') !== false) {
                                list($key, $v) = explode(':', $val);
                                if (empty($key) || empty($v)) continue;

                                $options[$key] = $v;
                            } else {
                                $options[$val] = $val;
                            }
                        }
                    } else {
                        continue;
                    }
                    if (empty($options)) continue;
                    if ($field->id_tipologia_form_fields == 8) {
                        $attributes = [
                            'class'                     => 'form-control form-item selectpicker',
                            'data-style'                => 'btn-flat btn-white',
                            'multiple'                  => 'multiple',
                            'data-actions-box'          => true,
                            'data-size'                 => 5,
                            'data-width'                => '100%',
                            'data-live-search'          => true,
                            'data-selected-text-format' => 'count>1',
                            'useEmpty'                  => false,
                            'title'                     => $field->placeholder,
                            'emptyText'                 => '---'
                        ];
                        $fname = $field->name . '[]';
                    } else {
                        $attributes = [
                            'class'                     => 'form-control form-item selectpicker',
                            'data-style'                => 'btn-flat btn-white',
                            'data-size'                 => 5,
                            'data-width'                => '100%',
                            'data-live-search'          => true,
                            'data-selected-text-format' => 'count>1',
                            'useEmpty'                  => false,
                            'title'                     => $field->placeholder,
                            'emptyText'                 => '---',
                        ];
                        $fname = $field->name;
                    }
                    $input = new Select(
                        $fname,
                        $options,
                        $attributes
                    );
                    break;
                case 7: //Checkbox
                    $input = new Check($field->name, ['labeled' => true, 'class' => 'input-checkbox']);
                    $tags->injectCssFromDi('/assets/site/css/iCheck/square/red.css');
                    $tags->injectJsFromDi('/assets/site/js/iCheck/icheck.min.js');
                    break;
            }
            if (isset($input)) {
                if ($field->obbligatorio) {
                    $input->setAttribute('required', $field->obbligatorio)->setAttribute('aria-required', $field->obbligatorio);
                }
                $input->setLabel($field->label);
                $input->setAttribute('placeholder', $field->placeholder);
                $form->add($input);
            } else {
                continue;
            }
        }

        $formEnt = \Forms::findFirst([
            'conditions' => 'key = ?1 AND id_applicazione = ?2',
            'bind'       => [1 => $form_key, 2 => Cms::getIstance()->id_application],
            'cache'      => [
                "key"      => "getFormByKey" . $form_key,
                "lifetime" => 12400
            ]
        ]);

        if (!$formEnt) return false;
        //$security = Di::getDefault()->get('security');
        $post_id = new Hidden('id_post');
        $post_id->setAttribute('value', $id_post)->setAttribute('hidden', true);
        $form->add($post_id);

        $form_id = new Hidden('id_form');
        $form_id->setAttribute('value', $formEnt->id)->setAttribute('hidden', true);
        $form->add($form_id);

        $form_key = new Hidden('form_key');
        $form_key->setAttribute('value', $formEnt->key)->setAttribute('hidden', true);
        $form->add($form_key);

        $form_csrf = new Hidden('csrf');
        $form_csrf->setAttribute('hidden', true);
        $form->add($form_csrf);

        return [
            'form'       => $form,
            'formEntity' => $formEnt,
            'formFields' => $formFields
        ];
    }
}