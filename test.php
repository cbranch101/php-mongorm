<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	MongORM::connect('test2');


	echo json_encode(iterator_to_array($data));
/* 	echo json_encode($testData); */
	
	
	$query = array('name' => 'John');
	$john = MongORM::for_collection('users')
		->find($query);
	
	$john->age = 100;
	$success = $john->update();
	
				
	
	
	
	