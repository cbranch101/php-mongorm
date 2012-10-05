<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	// test id 1223263
	MongORM::connect('aisle5Test');
	
	$test = array(
		'_id' => 1007,
		'id' => 1007,
		'name' => 'Frank',
		'willy' => 'nilly',
		'other' => 'monkey',
		'stuff' => 'mother',
		'skittles' => 'delicious',
	);		
		
	$user = MongORM::for_collection('users')
		->find_by_id($test['id']);
		
	if($user->has_contents()) {
		$user->set($test);
		$user->update();
	} else {
		$test['_id'] = $test['id'];
		MongORM::for_collection('users')
			->create_one($test);
	}
	
	$data = MongORM::for_collection('users')
		->find_by_id(1007)
	->as_array();
	
	echo json_encode($data);
		
		
/* 	echo json_encode($testData); */

	
				
	
	
	
	