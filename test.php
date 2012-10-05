<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	// test id 1223263
	MongORM::connect('aisle5Test');
	
	$test = array(
		'_id' => 1005,
		'name' => 'Frank',
		'other' => 'stuff',
	);
	
	MongORM::for_collection('users')
		->create_or_update_one($test);
		
	$data = MongORM::for_collection('users')
		->find_one(1005)
	->as_array();
	
	echo json_encode($data);
		
		
/* 	echo json_encode($testData); */

	
				
	
	
	
	