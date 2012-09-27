<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	MongORM::connect('test2');		
	
	MongORM::for_collection('records')
		->delete_many();
	
	$records = array(
		array(
			'name' => 'Steve',
			'age' => 10,
		),
		array(
			'name' => 'Tom',
			'age' => 10,
		),
		array(
			'name' => 'George',
			'age' => 10,
		),
	);
	
	MongORM::for_collection('records')
		->create_many($records);

	MongORM::for_collection('records')
		->delete_one(array('name' => 'Steve'));
	
	$data = MongORM::for_collection('records')
		->find_many()
		->as_array();
	
	echo json_encode($data);
	
		
/* 	echo json_encode($testData); */

	
				
	
	
	
	