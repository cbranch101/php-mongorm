<?php

	class MongORM {
		
		
		static $connection;
		static $database;
		
		/**
		 * connect function.
		 *
		 * Open a connection with Mongo database
		 * 
		 * @access public
		 * @static
		 * @param mixed $dbName
		 * @return void
		 */
		static function connect($databaseName, $host = null) {
			self::$connection = $host ? new Mongo($host) : new Mongo();	
			self::$database = self::$connection->$databaseName;	
		}
		
		static function for_collection($collectionName) {
			self::confirmDatabase();
		}
		
		static function confirmDatabase() {
			if(!self::$database) {
				Throw new Exception("Please call MongORM::connect() before proceeding");
			}
		}
		
		
	}
