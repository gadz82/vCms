<?php

class FormsController extends ControllerBase
{

    public function initialize()
    {
        $this->tag->setTitle('Form');
        parent::initialize();
    }

    public function submitAction()
    {
        if (!$this->request->isPost() || !$this->request->isAjax()) $this->response->redirect('404');
        $params = $this->request->getPost();
        if (!isset($params['form_key'], $params['id_post'])) $this->response->redirect('404');

        $formObject = \apps\site\library\Forms::getForm($params['form_key'], $params['id_post'], $this->di->get('tags'));

        /**
         * @var \Phalcon\Forms\Form
         */
        $form = $formObject['form'];
        $formModel = $formObject['formEntity'];
        $relatedPost = Posts::findFirst([
            'conditions' => 'id = ' . $params['id_post'],
        ]);

        /**
         * @var \Phalcon\Security
         */
        $security = \Phalcon\Di::getDefault()->get('security');
        if (!$security->checkToken()) {
            $this->response->setJsonContent(['success' => false, 'content' => self::getAlertMessageTemplate(false, 'Richiesta non valida')]);
            return $this->response;
        }
        $this->response->setHeader('csrfRKey', $this->security->getTokenKey());
        $this->response->setHeader('csrfRVal', $this->security->getToken());


        if (!$relatedPost) {
            $this->response->setJsonContent(['success' => false, 'content' => self::getAlertMessageTemplate(false, 'Richiesta non eseguibile')]);
            return $this->response;
        }
        $email_fields = [
            'request' => [],
            'params'  => []
        ];

        $subject = $relatedPost->titolo . " - " . $formModel->titolo;

        if (!$form->isValid($params)) {
            return $this->response->setJsonContent(['success' => false, 'content' => self::getAlertMessageTemplate(false, 'Form scaduto o non compialto correttamente')]);
        } else {
            $transaction = $this->beginTransaction();
            $formRequest = new FormRequests();
            $formRequest->id_form = $formModel->id;
            $formRequest->id_post = $relatedPost->id;
            $formRequest->letto = '0';
            $formRequest->data_creazione = (new DateTime())->format('Y-m-d H:i:s');
            $formRequest->attivo = 1;

            if (!$formRequest->save()) {
                $transaction->rollback();
                return $this->response->setJsonContent(['success' => false, 'content' => self::getAlertMessageTemplate(false, 'Errore nel salvataggio della richiesta')]);
            }

            foreach ($formObject['formFields'] as $field) {
                if ((!array_key_exists($field->name, $params) && $field->obbligatorio == 1) || ((empty($params[$field->name]) || is_null($params[$field->name])) && $field->obbligatorio == 1)) {
                    $transaction->rollback();
                    return $this->response->setJsonContent(['success' => false, 'content' => self::getAlertMessageTemplate(false, 'Campo ' . $field->label . ' mancante')]);
                }
                if (!isset($params[$field->name]) || empty($params[$field->name])) continue;
                $formRequestField = new FormRequestsFields();
                $formRequestField->id_form_request = $formRequest->id;
                $formRequestField->id_form = $formModel->id;
                $formRequestField->id_form_field = $field->id;
                $formRequestField->input_value = !is_array($params[$field->name]) ? $params[$field->name] : implode(', ', $params[$field->name]);
                $formRequestField->data_creazione = (new DateTime())->format('Y-m-d H:i:s');
                $formRequestField->attivo = 1;

                if ($field->id_tipologia_form_fields == 2) {
                    $email_fields['request'][$field->label] = \Phalcon\Tag::tagHtml('a', ['href' => 'mailto:' . $params[$field->name]]) . $params[$field->name] . \Phalcon\Tag::tagHtmlClose('a');
                } elseif ($field->id_tipologia_form_fields == 3) {
                    $email_fields['request'][$field->label] = \Phalcon\Tag::tagHtml('a', ['href' => 'tel:' . $params[$field->name]]) . $params[$field->name] . \Phalcon\Tag::tagHtmlClose('a');
                } else {
                    $email_fields['request'][$field->label] = !is_array($params[$field->name]) ? $params[$field->name] : implode(', ', $params[$field->name]);
                }
                //PhalconDebug::debug($formRequestField);
                if (!$formRequestField->save()) {
                    $transaction->rollback();
                    return $this->response->setJsonContent(['success' => false, 'content' => self::getAlertMessageTemplate(false, 'Errore nel salvataggio del campo ' . $field->label)]);
                }
            }
            $transaction->commit();

            $email_fields['params']['titolo_post'] = $relatedPost->titolo;
            $email_fields['params']['titolo_form'] = $subject;
            $email_fields['params']['link_site'] = $this->config->application->siteUri . '/' . $relatedPost->TipologiePost->slug . '/' . $relatedPost->slug;
            $email_fields['params']['link_admin'] = $this->config->application->siteUri . '/admin/posts/edit/' . $relatedPost->id;
            $email_fields['params']['link_form_request'] = $this->config->application->siteUri . '/admin/form_requests/edit/' . $formRequest->id;
            $email_fields['params']['post'] = $relatedPost;

            $to = null;
            $cc = [];
            $bcc = [];

            if (!empty($formModel->email_to)) {
                $to = $formModel->email_to;
                if (!empty($formModel->email_cc)) {
                    $cc = array_map('trim', explode(',', $formModel->email_cc));
                }
                if (!empty($formModel->email_bcc)) {
                    $bcc = array_map('trim', explode(',', $formModel->email_bcc));
                }
                $this->mailer->send($to, $cc, $bcc, $subject, $formModel->key, $email_fields, [], false);
            }

            if ($formModel->invio_utente == '1' && array_key_exists('email', $params)) {
                $this->mailer->send($params['email'], [], $bcc, $subject, $formModel->key, $email_fields, [], true);
            }

            return $this->response->setJsonContent(['success' => true, 'content' => self::getAlertMessageTemplate(true, 'Richiesta inviata!')]);
        }

    }

}