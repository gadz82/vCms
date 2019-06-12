<?php

class AjaxController extends ControllerBase
{

    public function initialize()
    {
        $this->tag->setTitle('Ajax');
        parent::initialize();
    }

    public function getChildFilterValuesAction()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            $params = $this->request->getPost();
            if (isset($params['id_filtro_valore'], $params['id_filtro'])) {
                $filtro = Filtri::findFirstById($params['id_filtro']);

                $fv = \FiltriValori::query()
                    ->innerJoin('PostsFiltri', 'pf.id_filtro_valore = FiltriValori.id AND pf.attivo = 1', 'pf')
                    ->innerJoin('Posts', 'p.id = pf.id_post AND p.id_tipologia_stato = 1 AND p.attivo = 1', 'p')
                    ->where('FiltriValori.id_filtro_valore_parent = ?1 AND FiltriValori.attivo = 1')
                    ->bind([
                        1 => $params['id_filtro_valore']
                    ])
                    ->groupBy('FiltriValori.id')
                    ->cache([
                        "key"      => "getChildFilterValue" . $params['id_filtro_valore'] . '.' . $params['id_filtro'],
                        "lifetime" => 12400
                    ])->execute();

                if ($fv && $filtro) {
                    $this->response->setJsonContent(['valori' => $fv->toArray(), 'filtro' => $filtro]);
                }
            }
        }
        return $this->response;
    }

    public function loginProxyAction()
    {
        if ($this->request->isPost() && $this->request->isAjax()) {

            if (!$this->request->hasPost('url')) {
                return $this->response->setJsonContent([
                    'success' => false,
                    'content' => self::getAlertMessageTemplate(false, 'Richiesta non valida')
                ]);
            }

            $params = $this->request->getPost();
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->request->getPost('url'));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            $result = curl_exec($ch);
            curl_close($ch);

            if ($result == 'NO') {
                return $this->response->setJsonContent([
                    'success' => true,
                    'content' => self::getAlertMessageTemplate(false, 'Siamo spiacenti ma il suo codice di accesso  e\' stato disabilitato. Per ulteriori informazioni contattare PAC2000A nella persona di Maurizio Prologo.'),
                    'msg'     => $result
                ]);
            }

            if (empty($result)) {
                return $this->response->setJsonContent([
                    'success' => false,
                    'content' => self::getAlertMessageTemplate(false, 'Autenticazione fallita'),
                    'msg'     => $result
                ]);
            }

            return $this->response->setJsonContent([
                'success' => true,
                'content' => self::getAlertMessageTemplate(true, 'Accesso eseguito'),
                'msg'     => $result
            ]);

        } else {
            return $this->response->redirect('/404');
        }
    }
}