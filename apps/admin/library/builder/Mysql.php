<?php

/**
 * Classe dbi
 *
 * Classe utilizzata per gestire la connession al database e l'esecuzione delle query.
 * La classe estende la classe singleton per assicurare di avere una singola istanza della classe.
 *
 */
class db
{
    public $last_error_number;
    public $last_error;
    public $num_rows;
    protected $db_host;
    protected $db_user;
    protected $db_pass;
    protected $db_name;
    protected $mysqli;

    /**
     * Metodo astratto della classe singleton.
     *
     * Viene utilizzato al posto del costruttore per implementare il pattern singleton.
     *
     * @see singleton::construct()
     */
    public function __construct($db_host, $db_user, $db_pass, $db_name)
    {
        $this->db_host = $db_host;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->db_name = $db_name;

        $this->mysqli = new mysqli ($db_host, $db_user, $db_pass, $db_name);
        if ($this->mysqli->connect_errno)
            die ('Connect Error (' . $this->mysqli->connect_errno . ')' . $this->mysqli->connect_error);
    }

    /**
     * Metodo per l'esecuzione di una query.
     *
     * Ritorna i valori sotto forma di array associativo monodimensionale.
     *
     * @param string $query
     * @param string $db_name
     * @param boolean $debug
     * @return boolean|array
     */
    public function db_query_max($query = '')
    {
        $return = [];

        $rs = $this->query($query);
        if (!is_object($rs))
            return false;

        if ($this->num_rows)
            $return = $rs->fetch_assoc();
        $rs->free();

        return $return;
    }

    /**
     * Metodo utilizzato per eseguire una query generica.
     *
     * @param string $query
     * @param string $db_name
     * @param boolean $debug
     * @return boolean|array
     */
    private function query($query)
    {
        $db_change = false;
        if (empty ($query))
            return false;
        if (empty ($db_name))
            $db_name = $this->db_name;
        if ($db_name != $this->db_name) {
            $this->mysqli->select_db($db_name);
            $db_change = true;
        }
        $this->last_error = '';
        $this->last_error_number = 0;

        if (!$this->mysqli->set_charset("utf8")) {
            printf("Error loading character set utf8: %s\n", $this->mysqli->error);
            $this->db_close();
        }
        $result = $this->mysqli->query($query);
        if ($result || empty ($this->mysqli->error)) {
            $this->num_rows = $this->mysqli->affected_rows;
            if ($db_change)
                $this->resetDb();
            return $result;
        } else {
            if ($db_change)
                $this->resetDb();
            $this->last_error_number = $this->mysqli->errno;
            $this->last_error = "(" . $this->mysqli->errno . ") " . $this->mysqli->error . " - " . $query;
            // if($debug) echo($this->last_error);
            return false;
        }
    }

    /**
     * Metodo per la chiusura della connessione al database.
     *
     * Verifica che la connessione sia aperta e non ci siano errori, quindi chiude la connessione. *
     */
    public function db_close()
    {
        if (!$this->mysqli->connect_errno && $this->mysqli instanceof mysqli)
            $this->mysqli->close();
    }

    private function resetDb()
    {
        $this->mysqli->select_db($this->db_name);
    }

    /**
     * Metodo per l'esecuzione di una query.
     *
     * Ritorna un singolo valore.
     *
     * @param string $query
     * @param string $db_name
     * @param boolean $debug
     * @return boolean|array
     */
    public function db_max($query = '')
    {
        $return = [];

        $rs = $this->query($query);
        if (!is_object($rs))
            return false;

        $return = $rs->fetch_row();
        $rs->free();

        return !empty ($return) ? reset($return) : $return;
    }

    public function db_show_colums($table_name, $dbname = '', $connection = false)
    {
        if (!$table_name)
            return false;
        if (!$dbname)
            $dbname = $this->db_name;

        $this->mysqli->select_db($dbname);

        $result = $this->query("SHOW COLUMNS FROM " . $table_name, $dbname, DEBUG_SQL);
        // if (!$result) { echo 'Non riesco ad eseguire la query: ' . $this->last_error(); exit; }
        if ($this->mysqli->affected_rows > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row ['name'] = $row ['Field'];
                $row ['flag'] = $row ['Extra'];
                $row ['type'] = $row ['Type'];

                $return [] = $row;
            }
        }
        return $return;
    }

    /**
     * Metodo per l'esecuzione di una query.
     *
     * Ritorna i valori nella forma array[i] = array(valore_1,valore_2,...,valore_n);
     *
     * @param string $query
     * @param string $db_name
     * @param boolean $debug
     * @return boolean|array
     */
    public function db_query_row($query = '')
    {
        $return = [];

        $rs = $this->query($query);
        if (!is_object($rs))
            return false;

        if (strpos($query, "SELECT") !== false) {
            for ($i = 0; $i < $this->num_rows; $i++)
                $return [$i] = $rs->fetch_row();
        }

        $rs->free();

        return $return;
    }

    /**
     * Metodo per l'esecuzione di una query.
     *
     * Ritorna i valori sotto forma di array monodimensionale
     *
     * @param string $query
     * @param string $db_name
     * @param boolean $debug
     * @return boolean|array
     */
    public function db_query_max_row($query = '')
    {
        $return = [];

        $rs = $this->query($query);
        if (!is_object($rs))
            return false;

        if (strpos($query, "SELECT") !== false) {
            for ($i = 0; $i < $this->num_rows; $i++) {
                $col = $rs->fetch_assoc();
                $return [$i] = reset($col);
            }
        }

        $rs->free();

        return $return;
    }

    /**
     * Metodo per l'esecuzione di una query.
     * Ritorna i valori nella forma array[i] = array('key'=>chiave,'value'=>valore);
     *
     * @param string $query
     * @param string $db_name
     * @param boolean $debug
     * @return boolean|array
     */
    public function db_query_j($query = '')
    {
        $return = [];

        $rs = $this->query($query);
        if (!is_object($rs))
            return false;

        if (strpos($query, "SELECT") !== false) {

            $temp = $rs->fetch_assoc();
            if (!$temp)
                return false;

            $keys = array_keys($temp);
            for ($i = 0; $i < count($keys); $i++) {
                $return [$i] ['key'] = $keys [$i];
                $return [$i] ['value'] = $temp [$keys [$i]];
            }
        } else {
            $return = $this->mysqli->affected_rows;
        }

        $rs->free();

        return $return;
    }

    /**
     * Metodo utilizzato per ritornare il numero di record risultanti dalla query.
     *
     *
     * @param string $query
     * @param string $db_name
     * @param boolean $debug
     * @return boolean|int
     */
    public function db_numrows($query = '')
    {
        $rs = $this->query($query);
        return !is_object($rs) ? false : $this->num_rows;
    }

    /**
     * Metodo utilizzato per ritornare il DESCRIBE di una tabella
     *
     * @param string $table
     * @param string $db_name
     * @param boolean $debug
     * @return boolean|Ambigous <boolean, multitype:NULL >
     */
    public function db_table_describe($table = '')
    {
        if (!$table)
            return false;

        $return = [];

        $query = "DESCRIBE " . $table;
        return $this->db_query($query);
    }

    /**
     * Metodo per l'esecuzione di una query.
     *
     * Se la query � una SELECT o DESCRIBE, ritorna i valori sotto forma di array associativo multidimensionale.
     *
     * @param string $query
     * @param string $db_name
     * @param boolean $debug
     * @return boolean|array
     */
    public function db_query($query = '')
    {
        $return = [];

        $rs = $this->query($query);

        if (((stripos($query, "SELECT") !== false && stripos($query, "INSERT") === false) || stripos($query, "DESCRIBE") !== false) && (stripos($query, "CREATE") === false || stripos($query, "CREATE") > 0)) {
            if (!is_object($rs))
                return false;
            for ($i = 0; $i < $this->num_rows; $i++)
                $return [$i] = $rs->fetch_assoc();
        } else if (strpos($query, 'UPDATE') !== false) {
            $return = $this->mysqli->affected_rows;
        } else {
            $return = $this->mysqli->affected_rows;
        }
        return $return;
    }

    /**
     * Metodo utilizzato per ritornare il nome delle colonne di una tabella.
     *
     *
     * @param string $table
     * @param string $db_name
     * @param boolean $debug
     * @return boolean|multitype:mixed
     */
    public function db_fields_table($table = '')
    {
        if (!$table)
            return false;

        $return = [];

        $query = "DESCRIBE " . $table;
        $rs = $this->query($query);
        if (!is_object($rs))
            return false;

        for ($i = 0; $i < $this->num_rows; $i++) {
            $col = $rs->fetch_assoc();
            $return [$i] = reset($col);
        }

        $rs->free();

        return $return;
    }

    /**
     * Metodo per il prepare di una query.
     *
     * Ritorna un oggetto di tipo mysqli_stmt con il quale bindare ed eseguire la query.
     *
     * @param string $query
     * @param boolean $debug
     * @return boolean|mysqli_stmt
     */
    public function db_prepare($query = '')
    {
        if (empty ($query))
            return false;

        if ($stmt = $this->mysqli->prepare($query)) {
            return $stmt;
        }

        return false;
    }

    /**
     * Metodo per il bind dei parametri da utilizzare dopo il prepare di una query.
     *
     * Esegue il bind e ritorna i valori dopo aver effettuato l'execute.
     *
     * @param mysqli_stmt $stmt
     * @param string $type
     * @param array $param
     * @return array
     */
    public function db_bind_param(mysqli_stmt $stmt, $type, $param)
    {
        $return = [];

        $params = array_merge([
            $type
        ], $param);
        call_user_func_array([
            $stmt,
            "bind_param"
        ], $this->ref_values($params));

        $stmt->execute();

        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc())
            $return [] = $row;

        $stmt->free_result();
        $stmt->close();

        return $return;
    }

    /**
     * Metodo utilizzato a partire da PHP 5.3 per il passaggio dei valori a call_user_func_array tramite riferimento.
     *
     * @param array $arr
     * @return array
     */
    private function ref_values($arr)
    {
        $refs = [];
        foreach ($arr as $key => $value)
            $refs [$key] = &$arr [$key];
        return $refs;
    }

    /**
     * Metodo per l'inizio della transazione sul database.
     */
    public function db_start_transaction()
    {
        $this->mysqli->autocommit(false);
    }

    /**
     * Metodo per il commit delle query effettuare a partire dall'inizio della transazione.
     */
    public function db_commit()
    {
        $this->mysqli->commit();
    }

    /**
     * Metodo per il rollback delle query effettuare a partire dall'inizio della transazione.
     */
    public function db_rollback()
    {
        $this->mysqli->rollback();
    }

    /**
     * Metodo per l'estrazione dell'ultimo indice inserito dalla query.
     *
     * @return int
     */
    public function db_last_insert_id()
    {
        return $this->mysqli->insert_id;
    }

    /**
     * Distruttore della classs
     *
     * Chiama il metodo db_close per chiudere la connessione al database
     */
    public function __destruct()
    {
        $this->db_close();
    }

    public function escape($string)
    {
        return $this->mysqli->escape_string($string);
    }
}

?>