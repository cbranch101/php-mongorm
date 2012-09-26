<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	
	MongORM::connect('test');		
	$tomData = array(
		'name' => 'Upton',
		'age' => 170,
	);
	
	$tom = MongORM::for_collection('friends')
		
		->create($tomData)
		->save();

	$tom2 = MongORM::for_collection('friends')
		->create($tomData)
		->save();
	
	$data = MongORM::for_collection('friends')
		->ensure_index(array('age' => true), array('unique' => true, 'dropDups' => true))
		->find_many()
	->as_array();
	
	echo json_encode($data);
	
	
		
		
	
