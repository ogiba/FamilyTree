<?php

require_once '/vendor/autoload.php';

spl_autoload_register(function($className){
    $className = str_replace("\\","/",$className);
    if (file_exists("src/".$className.".php")) {
        require_once "src/".$className.".php";
    }
});

$application = new Application();
$application->run();