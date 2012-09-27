<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	MongORM::connect('test2');


	echo json_encode(iterator_to_array($data));
/* 	echo json_encode($testData); */
	
	
			
	$users = array(
		array(
			'name' => 'Fred',
			'age' => 30,
		),
		array(
			'name' => 'John',
			'age' => 20,
		),
	);
		
	$users = MongORM::for_collection('users')
		->create_many($users);
	
	$query = array(
		'name' => 'John',
	);	
	
	$john = MongORM::for_collection('users')
		->find_one($query)
		->as_array();
	
	$john = array(
		'_id' => {
			'$id' => '1asdfajiia',
		},
		'name' => 'Fred',
		'age' => 30,
	);
		
	
	
	
	