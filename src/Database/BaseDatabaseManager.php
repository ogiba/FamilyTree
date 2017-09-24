<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 00:52
 */

namespace Database;

use Utils\SerializeManager;

class BaseDatabaseManager
{
    function createConnection(){
        $jsonString = file_get_contents('config.json', true);
        $config = new Config();
        SerializeManager::deserializeJson($jsonString, $config);//json_decode($jsonString, true);
        $mySql = mysqli_connect($config->host, $config->login, $config->pw, $config->table);
        $mySql->set_charset("utf8");
        return $mySql;
    }

    function &bindResult($stmt) {
        $variables = array();
        $data = array();
        $meta = $stmt->result_metadata();
        $d = false;

        if($meta == null)
            return $d;

        while ($field = $meta->fetch_field())
            $variables[] = &$data[$field->name]; // pass by reference

        call_user_func_array(array($stmt, 'bind_result'), $variables);

        return $data;
    }
}