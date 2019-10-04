<?php
use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{

    /**
     * JS assets loaded as external resources
     * @var array
     */
    protected static $js_assets = [
        'assets/site/js/plugins.js',
        'assets/site/js/lib/fitVids.js',
        'assets/site/js/lib/superFish.js',
        'assets/site/js/lib/smoothScroll.js',
        'assets/site/js/lib/jRespond.js',
        'assets/site/js/lib/appear.js',
        'assets/site/js/lib/animsition.js',
        'assets/site/js/lib/stellar.js',
        'assets/site/js/lib/swiper.js',
        'assets/site/js/lib/stickySidebar.js',
        'assets/site/js/lib/magnificPopup.js',
        'assets/site/js/jquery.lazy.min.js',
        'assets/site/js/lib/jquery.bootstrap.js',
        'assets/site/js/functions.js'
    ];
    /**
     * Css assets loaded as external resources
     * @var array
     */
    protected static $css_assets = [
        'assets/site/css/compiled/blog.css',
        'assets/site/css/compiled/shortcodes.css',
        'assets/site/css/compiled/sliders.css',
        'assets/site/css/bootstrap/bootstrap.css',
        'assets/site/css/animate.css',
        'assets/site/css/magnific-popup.css',
        'assets/site/css/compiled/typography.css',
        'assets/site/css/compiled/variables.css',
        'assets/site/css/compiled/widgets.css',
        'assets/site/css/swiper.css',
        'assets/site/css/compiled/dark.css',
        'assets/site/css/compiled/pagetitle.css',
        'assets/site/css/padd-mr.css',
        'assets/site/css/compiled/header.css',
        'assets/site/css/compiled/responsive.css',
        'assets/site/css/fonts/font-icons.css',
        'assets/site/css/compiled/custom.css'
    ];
    /**
     * Css assets loaded inline
     * @var array
     */
    protected static $css_inline_assets = [
        'assets/site/css/compiled/layouts.css',
        'assets/site/css/compiled/content.css',
        'assets/site/css/compiled/topbar.css',
        'assets/site/css/compiled/footer.css',
        'assets/site/css/compiled/mixins.css',
        'assets/site/css/compiled/extras.css',
        'assets/site/css/compiled/helpers.css'
    ];
    protected $controllerName;
    /**
     * @var \Phalcon\Db\Adapter\Pdo\Mysql
     */
    protected $connection;
    /**
     * Current request uri
     * @var
     */
    protected $currentUrl;
    /**
     * Current Application Code
     * @var string
     */
    protected $application;
    /**
     * Current application id
     * @var
     */
    protected $id_application;
    /**
     * Flag true if current user is logged in
     * @var bool
     */
    protected $isUserLoggedIn;
    /**
     * Current User basic info
     * @var array
     */
    protected $user;
    /**
     * Flag true if current user logged in is an administrator
     * @var bool
     */
    protected $isAdminLoggedIn;
    /**
     * Current application base url
     * @var string
     */
    protected $applicationUrl;

    /**
     * Utility to generate url-friendly conversion of a string
     * if minus parameter is false generate snake_case slug
     *
     * @param $text
     * @param bool $minus
     * @return mixed|string
     */
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

    /**
     * Return all meta availables for specific post_type
     *
     * @param $post_type_slug string
     * @return bool|mixed
     */
    public static function getPostTypeMetaFields($post_type_slug)
    {
        $app = \apps\site\library\Cms::getIstance()->application;
        $postTypeMetaFields = Options::findFirst([
            'conditions' => 'option_name = "columns_map_' . $app . '_' . $post_type_slug . '_meta" AND attivo = 1',
            'cache'      => [
                "key"      => $app . '_' . $post_type_slug . ".meta_fields",
                "lifetime" => 56400
            ]
        ]);
        if (!$postTypeMetaFields) return false;
        return json_decode($postTypeMetaFields->option_value, true);
    }

    /**
     * Return all filters available for a specific post_type
     *
     * @param $post_type_slug
     * @return bool|mixed
     */
    public static function getPostTypeFilterFields($post_type_slug)
    {
        $app = \apps\site\library\Cms::getIstance()->application;
        $postTypeFilterFields = Options::findFirst([
            'conditions' => 'option_name = "columns_map_' . $app . '_' . $post_type_slug . '_filter" AND attivo = 1',
            'cache'      => [
                "key"      => $app . '_' . $post_type_slug . ".filter_fields",
                "lifetime" => 56400
            ]
        ]);
        if (!$postTypeFilterFields) return false;
        return json_decode($postTypeFilterFields->option_value, true);
    }

    protected static function getAlertMessageTemplate($success = false, $message)
    {
        $tpl = $success ? 'success' : 'error';
        $view = \Phalcon\Di::getDefault()->get('view');
        return $view->getRender('alerts', $tpl, ['message' => $message], function ($view) {
            $view->setViewsDir("../apps/site/views/partials/");
            $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
        });
    }

    /**
     * Return available Post Types
     * @return TipologiePost|TipologiePost[]
     */
    public function getTipologiePost()
    {
        return TipologiePost::find([
            'conditions' => 'attivo = 1',
            'cache'      => [
                "key"      => "getPostTypes-frontend",
                "lifetime" => 12400
            ]
        ]);
    }

    /**
     * Controller boot function
     */
    protected function initialize()
    {
        $this->currentUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if ($this->di->getConfig()->debug->apc) apcu_clear_cache();

        /**
         * Class attributes population
         */
        $this->setAuthVars();
        $this->viewMobileDetection();
        $this->connection = $this->getDI()->getDb();
        $this->controllerName = $this->router->getModuleName() . '/' . $this->router->getControllerName();
        $this->application = \apps\site\library\Cms::getIstance()->application;
        $this->id_application = \apps\site\library\Cms::getIstance()->id_application;
        $this->view->appConfig = $this->config->application;
        // Prepend the application name to the title
        if (strpos($this->tag->getTitle(false), $this->config->application['appName']) === false) {
            $this->tag->appendTitle(' - ' . $this->config->application['appName']);
        }
        // $this->view->setTemplateAfter('main');

        /**
         * If is an ajax request shut off the view
         */
        if ($this->request->isAjax()) {
            // disattiva la renderizzazione della view
            $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);

            // setta il content type a json
            $this->response->setContentType('application/json', 'UTF-8');
            $this->response->setJsonContent([]);
        }

        /**
         * Mounting general view vars
         */
        $this->view->additiveJs = false;
        $this->view->additiveCss = false;
        $this->view->application = $this->application;
        $this->view->id_application = $this->id_application;
        $this->view->currentRoute = $this->getDi()->get('router')->getRewriteUri();
        $this->view->baseUrl = \apps\site\library\Cms::getIstance()->getApplicationUrl();
        $this->view->baseApplicationUrl = \apps\site\library\Cms::getIstance()->getApplicationUrl($this->application);
        $this->view->applicationHrefLang = \apps\site\library\Cms::getIstance()->applicationHrefLang;
        $this->applicationUrl = $this->view->applicationUrl = \apps\site\library\Cms::getIstance()->getApplicationUrl($this->application, true);

        $nr_js = count(self::$js_assets);
        $nr_css = count(self::$css_assets);
        $nr_inline_css = count(self::$css_inline_assets);

        /**
         * Assets managment
         * The cms apply different compilation rules on different Application environement
         *
         * DEVELOPMENT
         * Load all assets (except inline css) as differents resources, without minifcation
         *
         * STAGING
         * Load Assets merged in collections, without minify
         *
         * PRODUCTION
         * Load Assets merged in collections and minified
         */
        switch (APPLICATION_ENV) {
            case 'development':
                $jsSiteTheme = $this->assets->collection('jsSiteTheme');
                for ($i = 0; $i < $nr_js; $i++) {
                    $jsSiteTheme->addJs(self::$js_assets[$i]);
                }
                $jsSiteTheme->join(false);

                $cssSiteTheme = $this->assets->collection('cssSiteTheme');
                for ($n = 0; $n < $nr_css; $n++) {
                    $cssSiteTheme->addCss(self::$css_assets[$n]);
                }
                $cssSiteTheme->join(false);
                break;
            case 'staging':
                $jsSiteTheme = $this->assets->collection('jsSiteTheme')
                    ->setTargetPath('assets/site/js/min/site-theme.js')
                    ->setTargetUri('assets/site/js/min/site-theme.js');
                for ($i = 0; $i < $nr_js; $i++) {
                    $jsSiteTheme->addJs(self::$js_assets[$i]);
                }
                $jsSiteTheme->join(true);

                $cssSiteTheme = $this->assets->collection('cssSiteTheme')
                    ->setTargetPath('assets/site/css/min/site-theme.css')
                    ->setTargetUri('assets/site/css/min/site-theme.css');
                for ($n = 0; $n < $nr_css; $n++) {
                    $cssSiteTheme->addCss(self::$css_assets[$n]);
                }
                $cssSiteTheme->join(true);
                if(APPLICATION_ENV == 'production'){
                    $jsSiteTheme->addFilter(new Phalcon\Assets\Filters\Jsmin ());
                    $cssSiteTheme->addFilter(new Phalcon\Assets\Filters\Cssmin ());
                }
                break;
        }
        $inlineCssSiteTheme = $this->assets->collection('inlineCssSiteTheme')
            ->setTargetPath('assets/site/css/min/inline-site-theme.css')
            ->setTargetUri('assets/site/css/min/inline-site-theme.css');

        for ($x = 0; $x < $nr_inline_css; $x++) {
            $inlineCssSiteTheme->addCss(self::$css_inline_assets[$x]);
        }
        $inlineCssSiteTheme->join(true)->addFilter(new Phalcon\Assets\Filters\Cssmin());
    }

    /**
     * Handle Class variables that describes the user authentication status
     */
    protected function setAuthVars()
    {
        if (\apps\site\library\Cms::getIstance()->userLoggedIn) {
            $this->view->isUserLoggedIn = $this->isUserLoggedIn = true;
            $this->view->user = $this->user = $this->auth->getIdentity();
        } else {
            $this->view->isUserLoggedIn = $this->isUserLoggedIn = false;
        }

        $this->view->isAdminLoggedIn = $this->isAdminLoggedIn = \apps\site\library\Cms::getIstance()->adminLoggedIn;
        if ($this->isAdminLoggedIn && !$this->isUserLoggedIn) {
            $this->isUserLoggedIn = true;
            $this->user = [
                'id_users_groups' => 1
            ];
        }
    }

    /**
     * Check if request comes from a mobile device and if
     * the device is a smartphone or a tablet
     */
    protected function viewMobileDetection()
    {
        $this->view->isMobile = $this->mDetect->isMobile();
        $this->view->isTablet = $this->mDetect->isTablet();
        $this->view->isTelephone = !$this->mDetect->isTablet() && $this->mDetect->isMobile();

        $this->view->isAndroid = $this->mDetect->isAndroidOS();
        $this->view->isIos = $this->mDetect->isIos();
    }

    /**
     * Loading of assets collections for specfic component
     * @param $libraries
     * @param $name
     */
    protected function addLibraryAssets($libraries, $name)
    {
        $js_assets = [];
        $css_assets = [];
        $nr = count($libraries);
        for ($i = 0; $i < $nr; $i++) {

            switch ($libraries [$i]) {
                case 'instaFeed' :
                    $js_assets [] = 'assets/site/js/lib/instaFeed.js';
                    $js_assets [] = 'assets/site/js/instagram-feed.js';
                    break;
                //Youtube video player api (per video in bg)
                case 'YTPlayer' :
                    $js_assets [] = 'assets/site/js/lib/YTPLib.js';
                    break;
                case 'roundedSkills' :
                    $js_assets [] = 'assets/site/js/lib/easyPieChart.js';
                    $js_assets [] = 'assets/site/js/rounded-skill.js';
                    break;
                case 'colorAnimation' :
                    $js_assets [] = 'assets/site/js/lib/colorAnimation.js';
                    break;
                case 'infiniteScroll' :
                    $js_assets [] = 'assets/site/js/lib/infiniteScroll.js';
                    $js_assets [] = 'assets/site/js/infinite-scroll.js';
                    break;
                case 'jqueryValidate' :
                    $js_assets [] = 'assets/site/js/lib/jquery.validate.js';
                    break;
                case 'owlCarousel' :
                    $js_assets [] = 'assets/site/js/lib/owlcarousel.js';
                    break;
                case 'flexSlider' :
                    $js_assets [] = 'assets/site/js/lib/flexSlider.js';
                    break;
                case 'countDown' :
                    $js_assets [] = 'assets/site/js/lib/countdown.js';
                    break;
                default :
                    break;
            }
        }

        if (!empty ($js_assets)) {
            $r = $this->assets->collection('additiveJs')->setTargetPath('assets/site/js/min/' . $name . '.js')->setTargetUri('assets/site/js/min/' . $name . '.js');
            $nr = count($js_assets);
            for ($i = 0; $i < $nr; $i++) {
                $r->addJs($js_assets [$i]);
            }
            $r->join(true)->addFilter(new Phalcon\Assets\Filters\Jsmin ());
            $this->view->additiveJs = true;
        }
        if (!empty ($css_assets)) {
            $r = $this->assets->collection('additiveCss')->setTargetPath('assets/site/css/min/' . $name . '.css')->setTargetUri('assets/site/css/min/' . $name . '.css');
            $nr = count($css_assets);
            for ($i = 0; $i < $nr; $i++) {
                $r->addCss($css_assets [$i]);
            }
            $r->join(true)->addFilter(new Phalcon\Assets\Filters\Cssmin ());
            $this->view->additiveCss = true;
        }
    }

    /**
     * Return an istance of the Phalcon DB Transactions wrapper
     * @return \Phalcon\Mvc\Model\TransactionInterface
     */
    protected function beginTransaction()
    {
        $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
        return $manager->get();
    }

}
