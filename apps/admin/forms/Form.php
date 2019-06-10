<?php
namespace apps\admin\forms;

use Phalcon\Db\Column as Column;
use Phalcon\Forms\Element;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Email;
use Phalcon\Mvc\View;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\Regex as RegexValidator;

/**
 * Class Form
 * Utility per velocizzare la gestione e l'auto generazione dei Form in base al Model passato
 * @package apps\admin\forms
 */
class Form extends \Phalcon\Forms\Form {

    /**
     * @var
     */
    protected $attributes;

    /**
     * Nome del Model alla base dell'istanza
     * @var
     */
    protected $modelName;

    /**
     * @var View
     */
    protected $view;

    /**
     * Ovveride metodo di rendering
     * @param string $name
     * @param bool $renderError
     * @return string
     */
    public function render($name, $renderError = false) {
        $element = $this->get ( $name );
        $messages = $element->getMessages ();

        if ($messages->count () > 0) {
            $element->setAttribute ( 'class', $element->getAttribute ( 'class' ) . ' error' );
            $element->setUserOption ( 'group-class', $element->getUserOption ( 'group-class', '' ) . ' error' );
        }

        if ($element->getAttribute ( 'required' )) {
            $element->setLabel ( $element->getLabel () . ' *' );
        }

        $html = parent::render ( $name );
        if ($renderError) {
            $html .= $this->renderErrorFor ( $name );
        }

        return $html;
    }

    /**
     * Aggiunta error label dopo validazione input
     * @param $name
     * @return string
     */
    public function renderErrorFor($name) {
        $messages = $this->getMessagesFor ( $name );

        if ($messages->count () > 0) {
            $messages->rewind ();
            return '<div class="form-label-error">' . $this->getMessagesFor ( $name )->current () . '</div>';
        }

        return '';
    }

    /**
     * Utility per la modifica a runtime delle Label
     * @param string $name
     * @param array|null $options
     * @return string
     */
    public function label($name, array $options = null) {
        $element = $this->get ( $name );

        if ($element->getAttribute ( 'required' )) {
            $element->setLabel ( $element->getLabel () . ' *' );
        }

        return parent::label ( $name, null );
    }

    /**
     * Ritorna la classe di errore da aggiungere all'input
     * @param $name
     * @return string
     */
    public function hasErrorFor($name) {
        $messages = $this->getMessagesFor ( $name );
        return ($messages->count () > 0) ? 'has-error' : '';
    }

    /**
     * Wrappa il metodo per il ritorno dei token csrf
     * @todo attualmente non funziona, va fatto refactoring.
     * @return string
     */
    public function getCsrf() {
        return $this->security->getToken ();
    }

    /**
     * Estende il metodo add nativo di Phalcon filtrando l'element con i Decorators (se presenti)
     *
     * @param \Phalcon\Forms\ElementInterface $element
     * @param null $position
     * @param null $type
     * @return none
     */
    public function add(\Phalcon\Forms\ElementInterface $element, $position = null, $type = null)
    {
        $name = \Phalcon\Text::camelize(str_replace(' ', '_',  $element->getLabel()));
        $InputDecorator = "apps\\admin\\library\\decorators\\forms\\inputs\\".$this->modelName."\\Input".$name;
        if(class_exists($InputDecorator)){
            $element = (new $InputDecorator($element))->decorate();
        }
        parent::add($element, $position, $type);
    }

    /**
     * Genera i campi del form in base un \Phalcon\Mvc\Model
     *
     * @param \Phalcon\Mvc\Model $model
     * @param $modelName
     * @param array $excludeFields
     * @param array $orderFields
     * @param bool $multiSelect
     * @return array
     */
    protected function getAutoRenderByModel(\Phalcon\Mvc\Model $model, $modelName, $excludeFields = array(), $orderFields = array(), $multiSelect = false) {
        $autoFields = array ();
        $this->view = $this->getDi()->getView();
        $this->modelName = $modelName;

        if($this->view->exists('partials/forms/'.$this->modelName.'/before')) $this->view->{$this->modelName.'_before_form'} = 'partials/forms/'.$this->modelName.'/before';
        if($this->view->exists('partials/forms/'.$this->modelName.'/after')) $this->view->{$this->modelName.'_after_after'} = 'partials/forms/'.$this->modelName.'/after';

        $modelsManager = $model->getModelsManager ();
        $relations = $modelsManager->getRelations ( $modelName );

        $metaData = $model->getModelsMetaData ();
        $dataTypes = $metaData->getDataTypes ( $model );
        $this->attributes = $metaData->getAttributes ( $model );

        $array_relations = array ();
        $count = count ( $relations );
        for($i = 0; $i < $count; $i ++) {
            $array_relations [$relations [$i]->getFields ()] = $relations [$i]->getReferencedModel ();
        }

        $array_fields = array ();
        foreach ( $dataTypes as $field => $type ) {

            if (in_array ( $field, $excludeFields ))
                continue;

            switch ($type) {
                case Column::TYPE_INTEGER :
                    if (stripos ( $field, 'id_tipologia_' ) !== false) {
                        $array_fields ['select'] [] = $field;
                    } else {
                        $array_fields ['int'] [] = $field;
                    }
                    break;
                case Column::TYPE_DATE :
                case Column::TYPE_DATETIME :
                case 17 :
                    $array_fields ['date'] [] = $field;
                    break;
                case Column::TYPE_TEXT :
                    $array_fields ['textarea'] [] = $field;
                    break;
                default :
                    $array_fields ['text'] [] = $field;
                    break;
            }
        }

        foreach ( $array_fields as $type => $fields ) {

            switch ($type) {
                case 'int' :
                    $count = count ( $fields );
                    for($i = 0; $i < $count; $i ++) {
                        $label = ucfirst ( str_replace ( '_', ' ', $fields [$i] ) );
                        $f = new Numeric ( $fields [$i], array (
                            'class' => 'form-control',
                            'placeholder' => $label,
                            'min' => '1'
                        ) );
                        $f->setLabel ( $label );
                        $autoFields [$fields [$i]] = $f;
                    }
                    break;
                case 'text' :
                    $count = count ( $fields );
                    for($i = 0; $i < $count; $i ++) {

                        $label = ucfirst ( str_replace ( '_', ' ', $fields [$i] ) );
                        if (stripos ( $fields [$i], 'email' ) !== false) {
                            $f = new Email ( $fields [$i], array (
                                'class' => 'form-control',
                                'placeholder' => $label
                            ) );
                        } else {
                            $f = new Text ( $fields [$i], array (
                                'class' => 'form-control',
                                'placeholder' => $label
                            ) );
                        }
                        $f->setLabel ( $label );
                        $autoFields [$fields [$i]] = $f;
                    }
                    break;
                case 'textarea' :
                    $count = count ( $fields );
                    for($i = 0; $i < $count; $i ++) {

                        $label = ucfirst ( str_replace ( '_', ' ', $fields [$i] ) );
                        $f = new TextArea ( $fields [$i], array (
                            'class' => 'form-control',
                            'placeholder' => $label,
                            'rows' => '4',
                            'cols' => '50'
                        ) );
                        $f->setLabel ( $label );

                        $autoFields [$fields [$i]] = $f;
                    }
                    break;
                case 'date' :
                    $count = count ( $fields );
                    for($i = 0; $i < $count; $i ++) {

                        $label = ucfirst ( str_replace ( '_', ' ', $fields [$i] ) );
                        $f = new Text ( $fields [$i], array (
                            'class' => 'form-control range-datepicker',
                            'placeholder' => $label
                        ) );
                        $f->setLabel ( $label );

                        $autoFields [$fields [$i]] = $f;
                    }
                    break;
                case 'select' :
                    $count = count ( $fields );
                    for($i = 0; $i < $count; $i ++) {

                        $label = ucfirst ( str_replace ( '_', ' ', $fields [$i] ) );

                        if (isset ( $this->view->{$array_relations [$fields [$i]]} )) {
                            $select_field = $this->view->{$array_relations [$fields [$i]]};
                        } else {
                            $select_field = $array_relations [$fields [$i]]::find ( array (
                                'conditions' => 'attivo = 1',
                                'columns' => 'id,descrizione',
                                'order' => 'ordine ASC',
                                'cache' => array (
                                    'key' => $array_relations [$fields [$i]] . '-find',
                                    'lifetime' => 86400
                                )
                            ) );
                        }

                        if ($multiSelect) {
                            $f = new Select ( $fields [$i] . '[]', $select_field, array (
                                'class' => 'form-control selectpicker',
                                'using' => array (
                                    'id',
                                    'descrizione'
                                ),
                                'data-style' => 'btn-flat btn-white',
                                'multiple' => 'multiple',
                                'data-actions-box' => true,
                                'data-size' => 5,
                                'data-width' => '100%',
                                'data-live-search' => true,
                                'data-selected-text-format' => 'count>1',
                                'useEmpty' => false,
                                'emptyText' => '---'
                            ) );
                        } else {
                            $f = new Select ( $fields [$i], $select_field, array (
                                'class' => 'form-control selectpicker',
                                'using' => array (
                                    'id',
                                    'descrizione'
                                ),
                                'data-style' => 'btn-flat btn-white',
                                'data-size' => 5,
                                'data-width' => '100%',
                                'data-live-search' => true,
                                'data-selected-text-format' => 'count>1',
                                'useEmpty' => true,
                                'emptyText' => '---'
                            ) );
                        }

                        $f->setLabel ( $label );

                        $autoFields [$fields [$i]] = $f;
                    }
                    break;
            }
        }

        $sort_autoFields = array ();
        $count = count ( $this->attributes );
        for($i = 0; $i < $count; $i ++) {
            if (array_key_exists ( $this->attributes [$i], $autoFields ))
                $sort_autoFields [$this->attributes [$i]] = $autoFields [$this->attributes [$i]];
        }

        if (! empty ( $orderFields )) {
            $sort_autoFields_custom = array ();

            $count = count ( $orderFields );
            for($i = 0; $i < $count; $i ++) {
                if (array_key_exists ( $orderFields [$i], $autoFields ))
                    $sort_autoFields_custom [$orderFields [$i]] = $autoFields [$orderFields [$i]];
            }

            return array_unique ( array_merge ( $sort_autoFields_custom, $sort_autoFields ) );
        } else {

            return $sort_autoFields;
        }
    }

    /**
     * Riordina i campi in base all'array di ordinamento
     * @param $autoFields
     * @param array $orderFields
     * @return array
     */
    protected function reorderFields($autoFields, $orderFields = array()) {
        $sort_autoFields = array ();
        $count = count ( $this->attributes );
        for($i = 0; $i < $count; $i ++) {
            if (array_key_exists ( $this->attributes [$i], $autoFields ))
                $sort_autoFields [$this->attributes [$i]] = $autoFields [$this->attributes [$i]];
        }
        if (! empty ( $orderFields )) {
            $sort_autoFields_custom = array ();

            $count = count ( $orderFields );
            for($i = 0; $i < $count; $i ++) {
                if (array_key_exists ( $orderFields [$i], $autoFields ))
                    $sort_autoFields_custom [$orderFields [$i]] = $autoFields [$orderFields [$i]];
            }

            $arr = array_unique ( array_merge ( $sort_autoFields_custom, $sort_autoFields ) );
            $arr2 = array_diff ( $autoFields, $arr );

            return array_unique ( array_merge ( $arr, $arr2 ) );
        } else {

            $arr2 = array_diff ( $autoFields, $sort_autoFields );

            return array_unique ( array_merge ( $sort_autoFields, $arr2 ) );
        }
    }

    /**
     * Aggiunta validator di default
     * @param $autoFields
     * @param array $customValidation
     * @param array $exclude_required
     * @return mixed
     */
    protected function addValidateControl($autoFields, $customValidation = array(), $exclude_required = array()) {
        foreach ( $autoFields as $key => $f ) {
            if (! $f->getAttribute ( 'disabled' ) && ! in_array ( $key, $exclude_required )) {
                $f->setAttribute ( 'required', true );
                $f->addValidator ( new PresenceOf ( array (
                    'message' => 'Selezione un valore per [' . $f->getLabel () . ']'
                ) ) );
            }
            if (stripos ( $key, 'email' ) !== false && stripos ( $key, 'tipologia_' ) === false && ! in_array ( $key, $exclude_required )) {
                $f->addValidator ( new EmailValidator ( array (
                    'message' => 'Inserire un indirizzo e-mail un valore per [' . $f->getLabel () . ']'
                ) ) );
            }
            if (array_key_exists ( $key, $customValidation )) {
                if ($customValidation [$key] == 'regex_numeric') {
                    $f->addValidator ( new RegexValidator ( array (
                        'pattern' => '/^[0-9]*$/',
                        'message' => 'Inserire solo numeri per [' . $f->getLabel () . ']'
                    ) ) );
                } else {
                    $f->addValidator ($customValidation [$key]);
                }
            }
        }

        return $autoFields;
    }

    /**
     * Ritorna oggetto in sessione di Auth
     * @return mixed
     */
    protected function getAuth() {
        return $this->getDI ()->getSession ()->get ( 'auth-identity' );
    }

}