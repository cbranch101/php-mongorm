<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	
	MongORM::connect('aisle5Test');
	
	$data = MongORM::for_collection('users')
		->select('name')
		->find_by_id(678234993)
	->as_array();
	
	echo json_encode($data);
		
		
/* 	echo json_encode($testData); */

	
				
	
	
	
	