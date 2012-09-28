<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	
	MongORM::connect('test');
	
/*
	$user = MongORM::for_collection('users')
		->find_one();
		
	$user->name = 'Clay';
	
	$user->update();
*/
		
	MongORM::for_collection('final_test')
		->delete_many(array('x' => 'store'));
	
	$data = MongORM::for_collection('final_test')
		->find_many()
		->as_array();	
	echo json_encode($data);
		
		
/* 	echo json_encode($testData); */

	
				
	
	
	
	