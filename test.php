<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	
	MongORM::connect('test');		
	
	$data = MongORM::for_collection('users')
		->select('age')
		->find_many()
		->sort(array('age' => -1))
			->as_array();
	echo json_encode($data);
		
		
	
