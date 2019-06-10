<?php
use Phalcon\Mvc\Controller;

class ControllerBase extends Controller {
	protected $controllerName;
	protected $alert_messagge;

    /**
     * @var \Phalcon\Db\Adapter\Pdo
     */
    protected $connection;

    public static function slugify($text, $minus = false)
    {
        $r = $minus ? '-' : '_';
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', $r, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $r);

        // remove duplicate -
        $text = preg_replace('~-+~', $r, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

	protected function initialize() {
        //$this->view->disable();
        $this->connection = $this->getDI()->getDb();
		if ($this->di->getConfig()->debug->apc) apcu_clear_cache();

		$this->controllerName = $this->router->getModuleName().'/'.$this->router->getControllerName();

		// Prepend the application name to the title
		$this->tag->prependTitle ( 'CMS.IO - ' );
		// $this->view->setTemplateAfter('main');

		if ($this->request->isAjax ()) {
			// disattiva la renderizzazione della view
			$this->view->setRenderLevel ( \Phalcon\Mvc\View::LEVEL_ACTION_VIEW );

			// setta il content type a json
			$this->response->setContentType ( 'application/json', 'UTF-8' );
			$this->response->setJsonContent ( array () );
		}

	}

	protected function cacheKeyExists($key, $prefix = false) {
		$cache = $this->di->getModelsCache ();

		if ($prefix) {
			$cache_keys = $cache->queryKeys ( 'cmsio-cache-' );
			foreach ( $cache_keys as $k ) {
				if (strpos ( $k, 'cmsio-cache-' . $key ) !== false)
					return true;
			}
			return false;
		} else {
			return $cache->exists ( 'cmsio-cache-' . $key );
		}
	}

	protected function cacheKeyFlush($key, $prefix = false) {
		$cache = $this->di->getModelsCache ();

		if ($prefix) {
			$cache_keys = $cache->queryKeys ( 'cmsio-cache-' );
			foreach ( $cache_keys as $k ) {
				if (strpos ( $k, 'cmsio-cache-' . $key ) !== false) {
					$cache->delete ( str_replace ( 'cmsio-cache-', '', $k ) );
				}
			}
		} else {
			if ($cache->exists ( 'cmsio-cache-' . $key )) {
				$cache->delete ( $key );
			}
		}
	}

	protected function beginTransaction(){
        $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
        return $manager->get();
    }
}
