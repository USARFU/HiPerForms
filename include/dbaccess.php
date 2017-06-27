<?php
    
    /**
     * This file is responsible for creating and initializing the FileMaker object.
     * This object allows you to manipulate data in the database. To do so, simply 
     * include this file in the PHP file that needs access to the FileMaker database.
     */
    
    // include the FileMaker PHP API //
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require_once ("$root/FileMaker.php");
    
    //create the FileMaker Object
    $fm = new FileMaker();
    
    
    //Specify the FileMaker database
    $fm->setProperty('database', 'HiPer');
    
    //Specify the Host
    //$fm->setProperty('hostspec', 'http://localhost');
    $fm->setProperty('hostspec', 'http://hiperrugby.org');
    $fm->setProperty('username', 'web');
    $fm->setProperty('password', 'PfQskiRyvUpz7VbX4pbP');
