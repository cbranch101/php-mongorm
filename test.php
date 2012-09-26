<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	
	MongORM::connect('test');		
	$tomData = array(
		'name' => 'Tom',
		'age' => 20,
	);
	
	$tom = MongORM::for_collection('friends')
		->ensure_index(array('name' => 1), array('unique' => true, 'dropDups' => true))
		->create($tomData)
		->save();

	$tom2 = MongORM::for_collection('friends')
		->ensure_index(array('name' => 1))
		->create($tomData)
		->save();
	
	$data = MongORM::for_collection('friends')
		->find_many()
	->as_array();
	echo json_encode($data);
		
		
	
