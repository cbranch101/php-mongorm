<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	// test id 1223263
	MongORM::connect('test', 'localhost');	
		
	$testData = array(
		array(
			'_id' => 1,
			'store_id' => 3,
		),
		array(
			'_id' => 2,
			'store_id' => 4,
		),
	);
		
	MongORM::for_collection('other_users')
		->create_many($testData);
		
	$data = MongORM::for_collection('users');
	$otherData = MongORM::for_collection('other_users');
	$output = $data->find_many()->as_array();
	
	echo json_encode($output);
	
	
	

	
				
	
	
	
	