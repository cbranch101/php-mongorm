<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	
	MongORM::connect('test2');
	
/*
	$user = MongORM::for_collection('users')
		->find_one();
		
	$user->name = 'Clay';
	
	$user->update();
*/
	
	$results = MongORM::for_collection('users')
		->find_many()
		->as_array();
	
	echo json_encode($results);
		
		
/* 	echo json_encode($testData); */

	
				
	
	
	
	