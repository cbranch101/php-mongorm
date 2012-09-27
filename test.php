<?php

	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
	require_once('php_mongorm.php');
	
	MongORM::connect('test2');
		
	
	$allRecords = MongORM::for_collection('records')
		->find_many();
	$test = array(
		'x' => 100,
		'y' => 100,
	);
	$allRecords->set($test);
	$allRecords->update();
	
	$data = MongORM::for_collection('records')
		->find_many()
		->as_array();
	
	echo json_encode($data);
	
		
/* 	echo json_encode($testData); */

	
				
	
	
	
	