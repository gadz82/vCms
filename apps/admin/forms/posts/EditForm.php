<?php

namespace apps\admin\forms\posts;

use apps\admin\forms\Form;
use Phalcon\Di;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Check;
use Phalcon\Mvc\Model\Query\Builder;

class EditForm extends Form
{

    protected $id_tipologia_post = 1;
    protected $fields = [];
    protected $exclude_required = [];
    protected $custom_validation = [];

    public function initialize($entity = null, $options = [])
    {
        $this->id_tipologia_post = $entity->id_tipologia_post;
        $this->id_applicazione = $this->getDI()->getSession()->get('id_applicazione');

        $exclude_fields['Posts'] = ['id', 'data_creazione', 'data_aggiornamento', 'id_utente', 'attivo', 'id_tipologia_post'];
        $order_fields['Posts'] = ['id_applicazione', 'id_tipologia_stato', 'id_tipologia_post', 'slug', 'data_inizio_pubblicazione', 'data_fine_pubblicazione', 'titolo', 'excerpt', 'testo'];

        //Ai fini del js validator
        $post_optional_fields = ['excerpt', 'data_fine_pubblicazione', 'id_tipologia_stato', 'id_tipologia_post', 'applicazione'];
        $this->fields['Posts'] = $this->getAutoRenderByModel(new \Posts(), 'Posts', $exclude_fields['Posts'], $order_fields['Posts'], false);

        $this->fields['Posts']['id_tipologia_stato']
            ->setLabel('Stato post')
            ->setAttribute('grid_class', 'col-lg-6 col-sm-12')
            ->setAttribute('position', 'side')
            ->setAttribute('useEmpty', false);

        $select_app = isset($this->view->Applicazioni) ? $this->view->Applicazioni : \Applicazioni::find(['conditions' => 'attivo = 1 AND id = ' . $entity->id_applicazione, 'columns' => 'id,descrizione', 'order' => 'descrizione ASC']);

        $id_app = new Select('id_applicazione', $select_app, ['class' => 'form-control selectpicker', 'using' => ['id', 'descrizione'], 'data-style' => 'btn-flat btn-white', 'data-size' => 5, 'data-width' => '100%', 'data-live-search' => true, 'data-selected-text-format' => 'count>1', 'useEmpty' => false, 'emptyText' => '---']);

        $id_app->setLabel('Applicazione')
            ->setAttribute('position', 'side')
            ->setAttribute('value', $this->id_applicazione);
        $this->fields['Posts']['id_applicazione'] = $id_app;

        $this->fields['Posts']['testo']->setAttribute('class', 'form-control wysiwyg')->setAttribute('grid_class', 'col-xs-12 textarea-container')->setLabel('Testo');
        $this->fields['Posts']['testo']->setAttribute('rows', '12');

        $this->fields['Posts']['slug']->setAttribute('position', 'side');

        $this->fields['Posts']['data_inizio_pubblicazione']
            ->setAttribute('position', 'side')
            ->setAttribute('class', 'form-control date-format');

        $this->fields['Posts']['data_fine_pubblicazione']
            ->setAttribute('position', 'side')
            ->setAttribute('class', 'form-control date-format');

        $this->fields['Posts'] = $this->reorderFields($this->fields['Posts'], $order_fields['Posts']);

        $select_tags = isset($this->view->Tags) ? $this->view->Tags : \Tags::find([
            'conditions' => 'attivo = 1 AND id_applicazione = ?1',
            'bind'       => [1 => $this->id_applicazione],
            'columns'    => 'id,titolo', 'order' => 'id ASC'
        ]);

        $tags = new Select(
            'tags',
            $select_tags,
            [
                'name'                      => 'Tags[tags][]',
                'class'                     => 'form-control selectpicker',
                'using'                     => ['id', 'titolo'],
                'data-size'                 => 15,
                'data-width'                => '100%',
                'data-actions-box'          => 1,
                'data-live-search'          => true,
                'data-selected-text-format' => 'count>1',
                'multiple'                  => true,
                'useEmpty'                  => true,
                'emptyValue'                => 0,
                'emptyText'                 => 'Nessun Tag'
            ]
        );
        $this->fields['Tags']['tags'] = $tags;
        $this->fields['Tags']['tags']->setLabel('Segli i tag');

        $select_groups = isset($this->view->userGroups) ? $this->view->userGroups : \UsersGroups::find([
            'conditions' => 'attivo = 1',
            'columns'    => 'id,titolo',
            'order'      => 'id ASC'
        ]);

        $ug = new Select(
            'groups',
            $select_groups,
            [
                'name'                      => 'userGroups[groups][]',
                'class'                     => 'form-control selectpicker',
                'using'                     => ['id', 'titolo'],
                'data-size'                 => 15,
                'data-width'                => '100%',
                'data-selected-text-format' => 'count>1',
                'multiple'                  => true,
                'useEmpty'                  => true,
                'emptyValue'                => 0,
                'emptyText'                 => 'Tutti'
            ]
        );
        $this->fields['userGroups']['groups'] = $ug;
        $this->fields['userGroups']['groups']->setLabel('Scegli i gruppi utenti autorizzati');

        $this->generateMetaBoxes();
        /* PREPARE VALIDATION */
        $stato = isset($entity->id_tipologia_stato) ? $entity->id_tipologia_stato : 1;
        $this->prepareValidation($stato);
        /* FINE BLOCCO */

        /* ASSEGNAZIONE FIELDS->FORM */
        foreach ($this->fields as $key => $val) {
            if ($key == 'Posts' || $key == 'Tags' || $key == 'userGroups') {
                foreach ($val as $name => $field) {
                    if ($key == 'Posts') {
                        if (!in_array($field->getName(), $post_optional_fields)) $field->setAttribute('required', 'required');
                        $field->setName($key . '[' . $field->getName() . ']');
                    } else {
                        $field->setName($key . '[' . $field->getName() . ']');
                    }
                    $this->add($field);
                }
            } elseif ($key == 'meta') {
                foreach ($val as $mgroup => $mfield) {
                    foreach ($mfield as $metafield) {
                        $metafield->setName($key . '[' . $mgroup . '][' . $metafield->getName() . ']');
                        $this->add($metafield);
                    }
                }
            } elseif ($key == 'filtri') {
                foreach ($val as $fgroup => $ffield) {
                    foreach ($ffield as $filtrofield) {
                        $filtrofield->setName($key . '[' . $fgroup . '][' . $filtrofield->getName() . ']');
                        $this->add($filtrofield);
                    }
                }
            }
        }
        if (isset($entity)) {
            $this->add(new Hidden('id', ['hidden' => true, 'value' => $entity->id]));
        }
        /* FINE BLOCCO */
    }

    private function generateMetaBoxes()
    {

        $id_tipologia_post = $this->id_tipologia_post;
        $id_applicazione = $this->id_applicazione;

        $meta_groups = \MetaGroup::query()
            ->innerJoin('MetaGroupPostType', 'mg.id_meta_group = MetaGroup.id AND mg.attivo = 1', 'mg')
            ->where('MetaGroup.attivo = 1')
            ->andWhere('mg.id_tipologia_post = "' . $id_tipologia_post . '"')
            ->groupBy('MetaGroup.id')
            ->orderBy('priorita ASC')
            ->execute();
        $meta_boxes = [];

        $filtri_groups = \FiltriGroup::query()
            ->innerJoin('FiltriGroupPostType', 'fg.id_filtri_group = FiltriGroup.id AND fg.attivo = 1', 'fg')
            ->where('FiltriGroup.attivo = 1')
            ->andWhere('fg.id_tipologia_post = "' . $id_tipologia_post . '"')
            ->groupBy('FiltriGroup.id')
            ->orderBy('priorita ASC')
            ->execute();
        $filters = [];

        foreach ($meta_groups as $meta) {
            $meta_boxes[] = $meta->descrizione;
            $metafields = \Meta::find(['conditions' => 'id_meta_group =' . $meta->id, 'order' => 'priorita ASC']);
            $this->fields[$meta->descrizione] = [];

            $meta_view_k = \Phalcon\Text::camelize(str_replace(' ', '_', $meta->descrizione));
            if ($this->view->exists('partials/forms/' . $this->modelName . '/metaboxes/' . $meta_view_k . '/before')) $this->view->{$meta->descrizione . '_before'} = 'partials/forms/' . $this->modelName . '/metaboxes/' . $meta_view_k . '/before';
            if ($this->view->exists('partials/forms/' . $this->modelName . '/metaboxes/' . $meta_view_k . '/after')) $this->view->{$meta->descrizione . '_after'} = 'partials/forms/' . $this->modelName . '/metaboxes/' . $meta_view_k . '/after';

            foreach ($metafields as $metafield) {
                $tipologia_meta = $metafield->TipologieMeta->descrizione;
                switch ($tipologia_meta) {
                    case 'Decimale':
                    case 'Stringa':
                    case 'File':
                    case 'Intero':
                    case 'File Collection':
                        $input_name = $tipologia_meta !== 'File Collection' ? 'meta[' . $meta->descrizione . '][' . $metafield->id . ']' : 'meta[' . $meta->descrizione . '][' . $metafield->id . '][]';
                        $params = ['name' => $input_name, 'class' => 'form-control', 'placeholder' => $metafield->label];
                        if ($metafield->required) $params['required'] = true;
                        if ($tipologia_meta == 'File' || $tipologia_meta == 'File Collection') {
                            $params['class'] = 'form-control fileupload';
                            $params['id'] = 'fileupload';
                            $params['is_multi_upload'] = $tipologia_meta == 'File Collection' ? true : false;
                            $params['isfileupload'] = true;
                            $this->view->fileupload = true;
                        }
                        if ($metafield->hidden == '1') $params['class'] .= " hidden";

                        if (!empty($metafield->dataset)) {
                            $data_rows = explode('|', $metafield->dataset);
                            foreach ($data_rows as $r) {
                                list($data, $value) = explode(':', $r, 2);
                                $params['data-' . $data] = $value;
                            }
                        }

                        $this->fields['meta'][$meta->descrizione][$metafield->id] = new Text(
                            $metafield->id,
                            $params
                        );
                        $this->fields['meta'][$meta->descrizione][$metafield->id]->setLabel($metafield->label);
                        break;
                    case 'Date/Time':
                        $params = ['name' => 'meta[' . $meta->descrizione . '][' . $metafield->id . ']', 'class' => 'form-control date-format', 'grid_class' => 'col-md-6', 'placeholder' => $metafield->label];
                        if ($metafield->hidden == '1') $params['class'] .= " hidden";
                        if ($metafield->required) $params['required'] = true;

                        if (!empty($metafield->dataset)) {
                            $data_rows = explode('|', $metafield->dataset);
                            foreach ($data_rows as $r) {
                                list($data, $value) = explode(':', $r, 2);
                                $params['data-' . $data] = $value;
                            }
                        }

                        $this->fields['meta'][$meta->descrizione][$metafield->id] = new Text(
                            $metafield->id,
                            $params
                        );
                        $this->fields['meta'][$meta->descrizione][$metafield->id]->setLabel($metafield->label);
                        break;
                    case 'Select':
                        $dataset = [];
                        if (!empty($metafield->dataset)) {
                            $data_rows = explode('|', $metafield->dataset);
                            foreach ($data_rows as $r) {
                                list($value, $name) = explode(':', $r, 2);
                                $dataset[$value] = $name;
                            }
                        }
                        $params = [
                            'name'        => 'meta[' . $meta->descrizione . '][' . $metafield->id . ']',
                            'class'       => 'form-control selectpicker',
                            'data-size'   => 5,
                            'data-width'  => '100%',
                            'grid_class'  => 'col-md-6',
                            'placeholder' => $metafield->label,
                            'using'       => ['id', 'descrizione']
                        ];
                        if (!$metafield->required) {
                            $params['useEmpty'] = true;
                            $params['emptyText'] = '---';
                        } else {
                            $params['required'] = true;
                        }
                        if ($metafield->hidden == '1') $params['class'] .= " hidden";

                        $this->fields['meta'][$meta->descrizione][$metafield->id] = new Select(
                            $metafield->id,
                            $dataset,
                            $params
                        );
                        $this->fields['meta'][$meta->descrizione][$metafield->id]->setLabel($metafield->label);
                        break;
                    case 'Checkbox':
                        $params = ['name' => 'meta[' . $meta->descrizione . '][' . $metafield->id . ']', 'class' => 'icheck', 'placeholder' => $metafield->label];
                        if ($metafield->hidden == '1') $params['class'] .= " hidden";
                        if ($metafield->required) $params['required'] = true;
                        if (!empty($metafield->dataset)) {
                            $data_rows = explode('|', $metafield->dataset);
                            foreach ($data_rows as $r) {
                                list($value, $name) = explode(':', $r, 2);
                                $dataset[$value] = $name;
                            }
                        }

                        $this->fields['meta'][$meta->descrizione][$metafield->id] = new Check(
                            $metafield->id,
                            $params
                        );
                        $this->fields['meta'][$meta->descrizione][$metafield->id]->setLabel($metafield->label);
                        break;
                    case 'Testo':
                        $params = ['name' => 'meta[' . $meta->descrizione . '][' . $metafield->id . ']', 'class' => 'form-control', 'placeholder' => $metafield->label, 'rows' => '4', 'cols' => '50'];
                        if ($metafield->hidden == '1') $params['class'] .= " hidden";
                        if ($metafield->required) $params['required'] = true;

                        if (!empty($metafield->dataset)) {
                            $data_rows = explode('|', $metafield->dataset);
                            foreach ($data_rows as $r) {
                                list($value, $name) = explode(':', $r, 2);
                                $dataset[$value] = $name;
                            }
                        }

                        $this->fields['meta'][$meta->descrizione][$metafield->id] = new Textarea(
                            $metafield->id,
                            $params
                        );
                        $this->fields['meta'][$meta->descrizione][$metafield->id]->setLabel($metafield->label);
                        break;
                    case 'Html':
                        $params = ['name' => 'meta[' . $meta->descrizione . '][' . $metafield->id . ']', 'class' => 'form-control', 'placeholder' => $metafield->label, 'rows' => '4', 'cols' => '50', 'id' => 'html_code'];
                        if ($metafield->required) $params['required'] = true;
                        if ($metafield->hidden == '1') $params['class'] .= " hidden";

                        if (!empty($metafield->dataset)) {
                            $data_rows = explode('|', $metafield->dataset);
                            foreach ($data_rows as $r) {
                                list($value, $name) = explode(':', $r, 2);
                                $dataset[$value] = $name;
                            }
                        }

                        $this->fields['meta'][$meta->descrizione][$metafield->id] = new Textarea(
                            $metafield->id,
                            $params
                        );
                        $this->fields['meta'][$meta->descrizione][$metafield->id]->setLabel($metafield->label);
                        break;
                }

            }
        }
        $this->view->metaboxes = $meta_boxes;

        $filtersToWatch = [];
        foreach ($filtri_groups as $fgroup) {
            $filters[] = $fgroup->descrizione;

            $filtri = \Filtri::find([
                'conditions' => 'id_filtri_group = ?1 AND id_applicazione = ?2',
                'bind'       => [1 => $fgroup->id, 2 => $id_applicazione]
            ]);

            $this->fields['filtri'][$fgroup->descrizione] = [];

            $filter_view_k = \Phalcon\Text::camelize(str_replace(' ', '_', $fgroup->descrizione));
            if ($this->view->exists('partials/forms/' . $this->modelName . '/filterboxes/' . $filter_view_k . '/before')) $this->view->{$fgroup->descrizione . '_before'} = 'partials/forms/' . $this->modelName . '/filterboxes/' . $filter_view_k . '/before';
            if ($this->view->exists('partials/forms/' . $this->modelName . '/filterboxes/' . $filter_view_k . '/after')) $this->view->{$fgroup->descrizione . '_after'} = 'partials/forms/' . $this->modelName . '/filterboxes/' . $filter_view_k . '/after';

            foreach ($filtri as $filter) {
                $tipologia_filtro = $filter->TipologieFiltro->descrizione;
                $valori = \FiltriValori::find(
                    [
                        'conditions' => 'id_filtro = ' . $filter->id
                    ]
                );
                if (!$filter->id_filtro_parent) {
                    $add_dp = false;
                } else {
                    //$valori = [];
                    $add_dp = true;
                }
                switch ($tipologia_filtro) {
                    case 'Select':
                        $params = [
                            'name'        => 'filtri[' . $fgroup->descrizione . '][' . $filter->id . ']',
                            'class'       => 'form-control selectpicker',
                            'data-size'   => 5,
                            'data-width'  => '100%',
                            'grid_class'  => 'col-md-12',
                            'placeholder' => !empty($filter->descrizione) ? $filter->descrizione : $filter->titolo,
                            'using'       => ['id', 'valore']
                        ];
                        break;

                    case 'Multiselect':
                        $params = [
                            'name'                      => 'filtri[' . $fgroup->descrizione . '][' . $filter->id . '][]',
                            'class'                     => 'form-control selectpicker',
                            'using'                     => ['id', 'valore'],
                            'data-style'                => 'btn-flat btn-white',
                            'data-size'                 => 15,
                            'data-width'                => '100%',
                            'data-live-search'          => true,
                            'data-actions-box'          => 1,
                            'data-selected-text-format' => 'count>1',
                            'multiple'                  => true,
                            'placeholder'               => !empty($filter->descrizione) ? $filter->descrizione : $filter->titolo,
                            'emptyText'                 => 'Nessun Tag'
                        ];
                        break;
                }
                if (!$filter->required) {
                    $params['useEmpty'] = true;
                    $params['emptyText'] = '---';
                } else {
                    $params['required'] = true;
                }
                $this->fields['filtri'][$fgroup->descrizione][$filter->id] = new Select(
                    $filter->id,
                    $valori,
                    $params
                );
                $this->fields['filtri'][$fgroup->descrizione][$filter->id]->setLabel($filter->titolo);

                if ($add_dp) {
                    $filtersToWatch[] = $filter->id_filtro_parent;
                }
            }
        }
        $this->view->filtersToWatch = json_encode($filtersToWatch);
        $this->view->filtri_groups = $filters;

    }

    private function prepareValidation($id_tipologia_stato)
    {

        $arr_exclude_required = [];

        $this->custom_validation = [];
        foreach ($this->fields as $key => $val) {
            $arr_exclude_required['default'][$key] = [];
        }
        $arr_exclude_required['default']['Posts'] = ['data_inizio_pubblicazione', 'data_fine_pubblicazione', 'excerpt'];

        $this->compileValidation($id_tipologia_stato, $arr_exclude_required);

    }

    private function compileValidation($id_tipologia_stato, $arr_exclude_required)
    {

        $exclude_required = false;

        foreach ($arr_exclude_required as $key => $val) {
            if (in_array($id_tipologia_stato, explode('|', $key))) {
                $exclude_required = $val;
                break;
            }
        }

        $this->exclude_required = !$exclude_required ? $arr_exclude_required['default'] : $exclude_required;

        $arr_render_required = [];
        $arr_model = array_keys($this->fields);
        $count = count($arr_model);
        for ($i = 0; $i < $count; $i++) {

            $render = array_keys($this->fields[$arr_model[$i]]);
            foreach ($arr_exclude_required as $key => $val) {
                $render_required = array_diff($render, $val[$arr_model[$i]]);
                sort($render_required);
                $arr_render_required[$key][$arr_model[$i]] = $render_required;
            }

            if (!array_key_exists($arr_model[$i], $this->custom_validation)) {
                $this->custom_validation[$arr_model[$i]] = [];
            }
        }

        $this->view->render_required = json_encode($arr_render_required);

    }

    public function bind_custom(array $data, $entity, $whitelist = null)
    {
        parent::bind($data, $entity, $whitelist);
        foreach ($this->getElements() as $element) {
            $name = $element->getName();
            if (array_key_exists($name, $data)) {
                $element->setAttribute('value', $data[$name]);
                $element->setDefault($data[$name]);
            }
        }

    }

    public function beforeValidation()
    {

        $params = $this->request->getPost();
        $id_tipologia_stato = $params['Posts']['id_tipologia_stato'];

        $this->prepareValidation($id_tipologia_stato);
        foreach ($this->fields as $key => $val) {
            if ($key == 'Posts') {
                $this->fields[$key] = $this->addValidateControl($this->fields[$key], [], []);
            }
        }
    }

}
