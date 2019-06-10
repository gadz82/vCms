<?php

class Applicazioni extends \BaseModel {

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
    	parent::initialize ();

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

    public function afterCreate(){
        $con = Phalcon\Di::getDefault()->get('db');
        $query = '
        INSERT INTO 
          `applicazioni_routes` (`id_applicazione`, `id_tipologia_stato`, `id_tipologia_route`, `nome`, `path`, `params`, `ordine`, `data_creazione`, `data_aggiornamento`, `attivo`)
        VALUES
            ('.$this->id.', 1, 1, \'Home Page\', \'/\', \'{\"module\":\"site\",\"controller\":\"index\",\"action\":\"index\"}\', 1, \'2019-06-06 11:05:19\', \'2019-06-06 11:58:23\', 1),
            ('.$this->id.', 1, 1, \'404\', \'/404\', \'{\"module\":\"site\",\"controller\":\"errors\",\"action\":\"show404\"}\', 2, \'2019-06-06 11:39:42\', \'2019-06-06 12:04:29\', 1),
            ('.$this->id.', 1, 1, \'Pagina\', \'/{post_slug:[a-z\\-]+}\', \'{\"module\":\"site\",\"controller\":\"entity\",\"action\":\"read\",\"post_type_slug\":\"pagina\",\"params\":1}\', 3, \'2019-06-06 11:40:35\', \'2019-06-06 12:04:29\', 1),
            ('.$this->id.', 1, 1, \'List Tipologia Post\', \'/{post_type_slug:[a-z\\-]+}/\', \'{\"module\":\"site\",\"controller\":\"list\",\"action\":\"list\",\"post_type_slug\":1}\', 4, \'2019-06-06 11:42:45\', \'2019-06-06 12:04:30\', 1),
            ('.$this->id.', 1, 1, \'List Tipologia Post Filtrata\', \'/{post_type_slug:[a-z\\-]+}/:action/:params\', \'{\"module\":\"site\",\"controller\":\"list\",\"action\":2,\"post_type_slug\":1,\"params\":3}\', 5, \'2019-06-06 11:44:56\', \'2019-06-06 12:04:31\', 1),
            ('.$this->id.', 1, 1, \'Dettaglio Post\', \'/{post_type_slug:[a-z\\-]+}/{post_slug:[a-z0-9\\-]+}\', \'{\"module\":\"site\",\"controller\":\"entity\",\"action\":\"read\",\"post_type_slug\":1,\"post_slug\":2}\', 6, \'2019-06-06 11:47:53\', \'2019-06-06 12:04:32\', 1),
            ('.$this->id.', 1, 1, \'PDF Post\', \'/{post_type_slug:[a-z\\-]+}/{post_slug:[a-z0-9\\-]+}.pdf\', \'{\"module\":\"site\",\"controller\":\"pdf\",\"action\":\"read\",\"post_type_slug\":1,\"post_slug\":2}\', 7, \'2019-06-06 11:48:48\', \'2019-06-06 12:04:33\', 1),
            ('.$this->id.', 1, 1, \'User Area\', \'/user\', \'{\"module\":\"site\",\"controller\":\"users\",\"action\":\"index\"}\', 8, \'2019-06-06 11:49:36\', \'2019-06-06 12:04:34\', 1),
            ('.$this->id.', 1, 1, \'User Area Azione Specifica\', \'/user/:action\', \'{\"module\":\"site\",\"controller\":\"users\",\"action\":1,\"params\":2}\', 9, \'2019-06-06 11:50:03\', \'2019-06-06 12:04:35\', 1),
            ('.$this->id.', 1, 1, \'Endpoint Ajax\', \'/ajax/:action/:params\', \'{\"module\":\"site\",\"controller\":\"ajax\",\"action\":1,\"params\":2}\', 10, \'2019-06-06 11:50:43\', \'2019-06-06 12:04:37\', 1),
            ('.$this->id.', 1, 1, \'Form Request\', \'/forms/:action/:params\', \'{\"module\":\"site\",\"controller\":\"forms\",\"action\":1,\"params\":2}\', 11, \'2019-06-06 11:54:51\', \'2019-06-06 12:04:40\', 1),
            ('.$this->id.', 1, 1, \'Rendering Media\', \'/media/:action/:params\', \'{\"module\":\"site\",\"controller\":\"media\",\"action\":1,\"params\":2}\', 12, \'2019-06-06 11:55:18\', \'2019-06-06 12:04:41\', 1),
            ('.$this->id.', 1, 1, \'Sitemap\', \'/sitemap.xml\', \'{\"module\":\"site\",\"controller\":\"sitemap\",\"action\":\"index\"}\', 13, \'2019-06-06 11:55:59\', \'2019-06-06 12:04:41\', 1);
        ';
        $con->query($query);

        $tipologiePosts = TipologiePost::find();
        $option = \Options::findFirstByOptionName('reindex_queue');

        $tp_ids = [];
        foreach($tipologiePosts as $tp){
            $tp_ids[] = $tp->id;
        }

        if($option){
            $option->option_value = json_encode($tp_ids);;
            $option->save();
        } else {
            $option = new \Options();
            $option->option_name = 'reindex_queue';
            $option->option_value = json_encode($tp_ids);
            $option->save();
        }
    }

    public function afterDelete(){
        $routes = ApplicazioniRoutes::find([
            'conditions' => 'id_applicaizone = ?1',
            'bind' => [1 => $this->id]
        ]);

        if($routes){
            foreach($routes as $r){
                $r->delete();
            }
        }

    }

   
}
