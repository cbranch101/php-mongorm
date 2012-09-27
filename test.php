<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	MongORM::connect('test2');


	echo json_encode(iterator_to_array($data));
/* 	echo json_encode($testData); */
	
	
		
	$user = array(
		'name' => 'John',
		'age' => 20,
	);
		
	MongORM::for_collection('users')
		->insert($user);
	
	