<?php

class Applicazioni extends \BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_applicazione;

    /**
     *
     * @var integer
     * @Column(type="integer", length=2, nullable=false)
     */
    public $id_tipologia_stato;

    /**
     *
     * @var string
     * @Column(type="string", length=75, nullable=false)
     */
    public $titolo;
    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=false)
     */
    public $codice;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $descrizione;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $data_creazione;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $data_aggiornamento;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=false)
     */
    public $id_utente_admin;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $attivo;

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Applicazioni[]|Applicazioni
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Applicazioni
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->hasMany('id', 'ApplicazioniUtenti', 'id_applicazione', ['alias' => 'ApplicazioniUtenti', 'reusable' => true]);
        $this->hasMany('id', 'Posts', 'id_applicazione', ['alias' => 'Posts', 'reusable' => true]);
        $this->hasMany('id', 'Tags', 'id_applicazione', ['alias' => 'Tags', 'reusable' => true]);
        $this->hasMany('id', 'Filtri', 'id_applicazione', ['alias' => 'Filtri', 'reusable' => true]);
        $this->hasMany('id', 'Forms', 'id_applicazione', ['alias' => 'Forms', 'reusable' => true]);
        $this->hasMany('id', 'Blocks', 'id_applicazione', ['alias' => 'Blocks', 'reusable' => true]);
        $this->belongsTo('id_tipologia_applicazione', 'TipologieApplicazione', 'id', ['alias' => 'TipologieApplicazione', 'reusable' => true]);
        $this->belongsTo('id_tipologia_stato', 'TipologieStatoApplicazione', 'id', ['alias' => 'TipologieStatoApplicazione', 'reusable' => true]);
        $this->belongsTo('id_utente_admin', 'Utenti', 'id', ['alias' => 'Utenti', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'applicazioni';
    }

    public function afterCreate()
    {
        $con = Phalcon\Di::getDefault()->get('db');
        $query = '
        INSERT INTO 
          `applicazioni_routes` (`id_applicazione`, `id_tipologia_stato`, `id_tipologia_route`, `nome`, `path`, `params`, `ordine`, `data_creazione`, `data_aggiornamento`, `attivo`)
        VALUES
            (' . $this->id . ', 1, 1, \'Home Page\', \'/\', \'{\"module\":\"site\",\"controller\":\"index\",\"action\":\"index\"}\', 1, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'404\', \'/404\', \'{\"module\":\"site\",\"controller\":\"errors\",\"action\":\"show404\"}\', 2, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'Pagina\', \'/{post_slug:[a-z\\-]+}\', \'{\"module\":\"site\",\"controller\":\"entity\",\"action\":\"read\",\"post_type_slug\":\"pagina\",\"params\":1}\', 3, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'List Tipologia Post\', \'/{post_type_slug:[a-z\\-]+}/\', \'{\"module\":\"site\",\"controller\":\"list\",\"action\":\"list\",\"post_type_slug\":1}\', 4, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'List Tipologia Post Filtrata\', \'/{post_type_slug:[a-z\\-]+}/:action/:params\', \'{\"module\":\"site\",\"controller\":\"list\",\"action\":2,\"post_type_slug\":1,\"params\":3}\', 5, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'Dettaglio Post\', \'/{post_type_slug:[a-z\\-]+}/{post_slug:[a-z0-9\\-]+}\', \'{\"module\":\"site\",\"controller\":\"entity\",\"action\":\"read\",\"post_type_slug\":1,\"post_slug\":2}\', 6, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'PDF Post\', \'/{post_type_slug:[a-z\\-]+}/{post_slug:[a-z0-9\\-]+}.pdf\', \'{\"module\":\"site\",\"controller\":\"pdf\",\"action\":\"read\",\"post_type_slug\":1,\"post_slug\":2}\', 7, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'User Area\', \'/user\', \'{\"module\":\"site\",\"controller\":\"users\",\"action\":\"index\"}\', 8, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'User Area Azione Specifica\', \'/user/:action\', \'{\"module\":\"site\",\"controller\":\"users\",\"action\":1,\"params\":2}\', 9, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'Endpoint Ajax\', \'/ajax/:action/:params\', \'{\"module\":\"site\",\"controller\":\"ajax\",\"action\":1,\"params\":2}\', 10, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'Form Request\', \'/forms/:action/:params\', \'{\"module\":\"site\",\"controller\":\"forms\",\"action\":1,\"params\":2}\', 11, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'Rendering Media\', \'/media/:action/:params\', \'{\"module\":\"site\",\"controller\":\"media\",\"action\":1,\"params\":2}\', 12, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'Sitemap\', \'/sitemap.xml\', \'{\"module\":\"site\",\"controller\":\"sitemap\",\"action\":\"index\"}\', 13, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'Api Root\', \'/api\', \'{\"module\":\"api\",\"controller\":\"api\",\"action\":\"index\"}\', 14, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'Api Controller\', \'/api/:controller\', \'{\"module\":\"api\",\"controller\":1,\"action\":\"index\"}\', 15, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'Api Taxonomy services\', \'/api/taxonomies/:action(/:params)\', \'{\"module\":\"api\",\"controller\":\"taxonomy\",\"action\":1,\"params\":2}\', 16, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'Listing Post Type\', \'/api/entities/{post_type_slug:[a-z\\-]+}/\', \'{\"module\":\"api\",\"controller\":\"list\",\"action\":\"fetch\",\"post_type_slug\":1}\', 17, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'Listing Post Type with Filters\', \'/api/entities/{post_type_slug:[a-z\\-]+}/:action/:params\', \'{\"module\":\"api\",\"controller\":\"list\",\"action\":2,\"post_type_slug\":1,\"params\":3}\', 18, NOW(), NOW(), 1),
            (' . $this->id . ', 1, 1, \'Api Entity Detail\', \'/api/entities/read/{post_type_slug:[a-z\\-]+}/{post_slug:[0-9{11}]+}\', \'{\"module\":\"api\",\"controller\":\"entity\",\"action\":\"read\",\"post_type_slug\":1,\"params\":2}\', 19, NOW(), NOW(), 1);
        ';
        $con->query($query);

        $tipologiePosts = TipologiePost::find();
        $option = \Options::findFirstByOptionName('reindex_queue');

        $tp_ids = [];
        foreach ($tipologiePosts as $tp) {
            $tp_ids[] = $tp->id;
        }

        if ($option) {
            $option->option_value = json_encode($tp_ids);;
            $option->save();
        } else {
            $option = new \Options();
            $option->option_name = 'reindex_queue';
            $option->option_value = json_encode($tp_ids);
            $option->save();
        }
    }

    /**
     * Step 1 : Delete Routes
     * Step 2 : Delete Domini
     * Step 3 : Delete Utenti
     * Step 4 : Delete Posts
     * Step 5 : Delete Blocks
     * Steo 6 : Form in status Disabled(3)
     * Step 7 : Delete Flat Tables
     */
    public function triggerDelete()
    {
        /*
         * Step 1
         */
        $routes = ApplicazioniRoutes::find([
            'conditions' => 'id_applicazione = ?1',
            'bind'       => [1 => $this->id]
        ]);

        if ($routes) {
            foreach ($routes as $r) {
                $r->delete();
            }
        }

        /*
         * Step 2
         */
        $domini = ApplicazioniDomini::find([
            'conditions' => 'id_applicazione = ?1',
            'bind'       => [1 => $this->id]
        ]);

        if ($domini) {
            foreach ($domini as $d) {
                $d->delete();
            }
        }

        /*
         * Step 3
         */
        $utenti = ApplicazioniUtenti::find([
            'conditions' => 'id_applicazione = ?1',
            'bind'       => [1 => $this->id]
        ]);

        if ($utenti) {
            foreach ($utenti as $u) {
                $u->delete();
            }
        }

        /*
         * Step 4
         */
        $posts = Posts::find([
            'conditions' => 'id_applicazione = ?1',
            'bind'       => [1 => $this->id]
        ]);

        if ($posts) {
            foreach ($posts as $post) {
                $post->delete();
                $post->triggerSave(true);
            }
        }

        /*
         * Step 5
         */
        $blocks = Blocks::find([
            'conditions' => 'id_applicazione = ?1',
            'bind'       => [1 => $this->id]
        ]);

        if ($blocks) {
            foreach ($blocks as $block) {
                $block->delete();
            }
        }

        /*
         * Step 6
         */
        $forms = Forms::find([
            'conditions' => 'id_applicazione = ?1',
            'bind'       => [1 => $this->id]
        ]);

        if ($forms) {
            foreach ($forms as $form) {
                $form->id_tipologia_stato = 3;
                $form->save();
            }
        }

        /*
         * Step 7
         */
        $eventsManager = new Phalcon\Events\Manager();
        $eventsManager->attach('dispatch:afterDeleteApplication', new \apps\admin\plugins\FlatTablesManagerPlugin());
        $eventsManager->fire('dispatch:afterDeleteApplication', $this);
    }

}
