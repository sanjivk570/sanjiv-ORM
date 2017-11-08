<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'config.php';
require_once 'Libraries/Logger.php';
require_once 'Libraries/Database/DatabaseInterface.php';
require_once 'Libraries/Database/Database.php';

$object = new Database\Database;
$test = $object->select('users');
$test = $object->fetch();
print_r($test);

