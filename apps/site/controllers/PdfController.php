<?php

class PdfController extends EntityController
{
    private $params;

    public function initialize()
    {
        parent::initialize();
        $this->response->setHeader('X-Robots-Tag', 'noindex');
    }

    public function readAction()
    {

        $this->view->disable();

        $post_type = $this->dispatcher->getParam('post_type_slug');
        $slug = $this->dispatcher->getParam('post_slug');
        $entity = $this->getPostBySlug($slug, $post_type);

        if (!$this->view->exists('partials/pdf/' . $post_type . '/read')) {
            return $this->response->redirect('/' . $post_type . '/' . $slug);
        }

        $pdf_template = $this->view->exists('partials/pdf/' . $post_type . '/' . $this->params['slug']) ? $this->params['slug'] : 'read';

        $content = $this->view->getRender(
            $post_type,
            $pdf_template,
            ['post' => $entity, 'base_url' => $this->config->application->protocol . $this->config->application->siteUri . '/'],
            function ($view) {
                $view->setViewsDir("../apps/site/views/partials/pdf");
                $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
            }
        );
        $this->di->get('debugbar')->disable();
        try {
            $html2pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'it');
            //echo $content;exit();
            $html2pdf->writeHTML($content);
            $html2pdf->output($entity->titolo . '.pdf', 'D');
        } catch (\Spipu\Html2Pdf\Exception\Html2PdfException $e) {
            echo 'html2pdf exc<br>';
            print_r($e->getMessage());
        } catch (\Spipu\Html2Pdf\Exception\HtmlParsingException $e) {
            echo 'html parsing<br>';
            print_r($e->getMessage());
        }
        exit();
    }

}