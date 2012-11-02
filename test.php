<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	// test id 1223263
	MongORM::connect('aisle5Test');
	MongORM::for_collection('users')
		->delete_many();
	
		
	$testData = array(
		array(
			'_id' => 1,
			'store_id' => 1,
		),
		array(
			'_id' => 2,
			'store_id' => 2,
		),
	);
		
	MongORM::for_collection('users')
		->create_many($testData);
		
	$data = MongORM::for_collection('users')
		->find_by_id(1);
		
	echo json_encode($data->_id);

	
				
	
	
	
	