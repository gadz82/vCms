<?php
/**
 * Created by PhpStorm.
 * User: Francesco
 * Date: 29/05/2019
 * Time: 11:03
 */

namespace apps\admin\plugins;


use Phalcon\Config;
use Phalcon\Di;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Event;

class FrontendGeneratorPlugin extends Plugin
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * Site View Dir
     * @var string
     */
    protected $view;

    /**
     * FrontendGeneratorPlugin constructor.
     */
    public function __construct()
    {
        $this->config = Di::getDefault()->get('config');
        $this->view = $this->config->application->siteViewsDir;
    }

    /**
     * @param Event $event
     * @param \TipologiePost $postType
     */
    public function afterCreatePostType(Event $event, \TipologiePost $postType)
    {
        if (file_exists($this->view . DIRECTORY_SEPARATOR . $postType->slug)) {
            unlink($this->view . DIRECTORY_SEPARATOR . $postType->slug);
        }
        $this->createViewScaffold($postType);

        if (file_exists($this->view . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . $postType->slug)) {
            unlink($this->view . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . $postType->slug);
        }
        $this->createViewPartials($postType);
    }

    /**
     * @param \TipologiePost $postType
     */
    protected function createViewScaffold(\TipologiePost $postType)
    {
        $dir = $this->view . DIRECTORY_SEPARATOR . $postType->slug;
        mkdir($dir);
        touch($dir . DIRECTORY_SEPARATOR . 'list.volt');
        touch($dir . DIRECTORY_SEPARATOR . 'read.volt');
        $readTpl = file_get_contents($this->view . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'scaffold' . DIRECTORY_SEPARATOR . 'read.volt');
        $listTpl = file_get_contents($this->view . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'scaffold' . DIRECTORY_SEPARATOR . 'list.volt');

        file_put_contents($dir . DIRECTORY_SEPARATOR . 'read.volt', $readTpl);
        file_put_contents($dir . DIRECTORY_SEPARATOR . 'list.volt', $listTpl);

    }

    /**
     * @param \TipologiePost $postType
     */
    protected function createViewPartials(\TipologiePost $postType)
    {
        $dir = $this->view . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . $postType->slug;
        mkdir($dir);
        $carouselTpl = file_get_contents($this->view . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'scaffold' . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'carousel.volt');
        $carouselTpl = str_replace('<!-- SLUG_POST_TYPE !-->', $postType->slug, $carouselTpl);
        $carouselTpl = str_replace('<!-- TITOLO_POST_TYPE !-->', $postType->descrizione, $carouselTpl);

        $lastTpl = file_get_contents($this->view . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'scaffold' . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'last.volt');
        $lastTpl = str_replace('<!-- SLUG_POST_TYPE !-->', $postType->slug, $lastTpl);
        $lastTpl = str_replace('<!-- TITOLO_POST_TYPE !-->', $postType->descrizione, $lastTpl);

        $oneEntryTpl = file_get_contents($this->view . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'scaffold' . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'oneEntry.volt');
        $oneEntryTpl = str_replace('<!-- SLUG_POST_TYPE !-->', $postType->slug, $oneEntryTpl);
        $oneEntryTpl = str_replace('<!-- TITOLO_POST_TYPE !-->', $postType->descrizione, $oneEntryTpl);

        file_put_contents($dir . DIRECTORY_SEPARATOR . 'carousel.volt', $carouselTpl);
        file_put_contents($dir . DIRECTORY_SEPARATOR . 'last.volt', $lastTpl);
        file_put_contents($dir . DIRECTORY_SEPARATOR . 'oneEntry.volt', $oneEntryTpl);

    }

    /**
     * @param Event $event
     * @param \TipologiePost $postType
     */
    public function afterEditPostType(Event $event, \TipologiePost $postType)
    {
        if (!file_exists($this->view . DIRECTORY_SEPARATOR . $postType->slug)) {
            $this->createViewScaffold($postType);
        }
        if (!file_exists($this->view . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . $postType->slug)) {
            $this->createViewPartials($postType);
        }
    }

    /**
     *
     */
    public function afterDeletePostType()
    {
        if (file_exists($this->view . DIRECTORY_SEPARATOR . $postType->slug)) {
            unlink($this->view . DIRECTORY_SEPARATOR . $postType->slug);
        }
        if (file_exists($this->view . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . $postType->slug)) {
            unlink($this->view . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . $postType->slug);
        }
    }

}