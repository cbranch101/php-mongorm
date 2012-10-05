<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	// test id 1223263
	MongORM::connect('aisle5Test');
	
	$data = MongORM::for_collection('users')
		->id_exists(1223263);
	
	echo json_encode($data);
		
		
/* 	echo json_encode($testData); */

	
				
	
	
	
	