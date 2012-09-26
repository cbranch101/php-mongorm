<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
/* 	MongORM::connect('test'); */
	MongORM::for_collection('test');
