<?php
if(!file_exists('.cmsinstalled')){

    $config = include __DIR__.'/../apps/config/config.php';
    $con = new Phalcon\Db\Adapter\Pdo\Mysql(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname
    ));

    $con->connect();
    $lines = file(__DIR__.'/../installdata.sql');
    $templine = "";
    foreach ($lines as $line)
    {

        if (substr($line, 0, 2) == '--' || $line == '')
            continue;

        $templine .= $line;
        if (substr(trim($line), -1, 1) == ';')
        {
            // Perform the query
            $con->query($templine);
            // Reset temp variable to empty
            $templine = '';
        }
    }
    touch(__DIR__.'/.cmsinstalled');
    header('Location: /admin');
} else {
    header('Location: /admin');
}
