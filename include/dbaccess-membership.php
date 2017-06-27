<?php

/**
 * This file is responsible for creating and initializing the FileMaker object.
 * This object allows you to manipulate data in the database. To do so, simply
 * include this file in the PHP file that needs access to the FileMaker database.
 */

// include the FileMaker PHP API //
require_once("$root/FileMaker.php");

//create the FileMaker Object
$fm = new FileMaker();


//Specify the FileMaker database
$fm->setProperty('database', 'HiPer_web');

//Specify the Host
//$fm->setProperty('hostspec', 'http://localhost');
$fm->setProperty('hostspec', 'http://hiperrugby.org');
$fm->setProperty('username', 'web-membership');
$fm->setProperty('password', 'Js0oyh2Fq4PGhGlVYfvK');
