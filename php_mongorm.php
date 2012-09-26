<?php

	class MongORM {
		
		
		static $connection;
		static $database;
		static $self;
		static $instance;
		static $collection;
		static $query;
		static $data;
		static $fields;
		static $cursor;
		
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
		
		static function delete_all() {
			self::$collection->remove();
		}
				
		static function for_collection($collectionName) {
			self::confirmDatabase();
			self::$collection = self::$database->$collectionName;
			if(self::$instance === null) {
				self::$instance = new self;
			}
			return self::$instance;
		}
		
		public function as_array() {
			return iterator_to_array(self::$cursor);
		}
		
		public function create($data = null) {
			self::$data = $data !== null ? $data : array();
			return $this;
		}
		
		public function save() {
			self::$collection->insert(self::$data);
		}
		
		public function select($fieldsToSelect) {
			if(is_array($fieldsToSelect)) {
				foreach($fieldsToSelect as $field) {
					self::$fields[$field] = true;
				}
			} else {
				self::$fields[$fieldsToSelect] = true;
			}
			return $this;
		}
		
		public function ensure_index($indexes, $options = array()) {
			self::$collection->deleteIndexes();
			self::$collection->ensureIndex($indexes, $options);
			return $this;
		}
		
		public function sort($fieldsToSortBy) {
			self::$cursor->sort($fieldsToSortBy);
			return $this;
		}
		
		public function find_one($query = null) {
			self::$cursor = self::sendQuery('findOne', $query);
			return $this;
		}
		
		public function find_many($query = null) {
			self::$cursor = self::sendQuery('find', $query);
			return $this;
		}
		
		public function sendQuery($queryType, $query) {
			if(self::$query) {
				self::$query = array_merge($query, self::$query);
			} else {
				self::$query = $query;
			}
			$args = array();
			$args[0] = self::$query === null ? array() : self::$query;
			$args[1] = self::$fields === null ? array() : self::$fields;
			return call_user_func_array(array(self::$collection, $queryType), $args);
		}
		
		public function getId() {
			return self::$data['_id'];
		}
		
		
		private function get($key) {
			return isset(self::$data[$key]) ? self::$data[$key] : null;
		}
		
		private function set($key, $value) {
			if(!isset(self::$data[$key])) {
				self::$data[$key] = array();
			}
			self::$data[$key] = $value;
		} 
		
		static function confirmDatabase() {
			if(!self::$database) {
				Throw new Exception("Please call MongORM::connect() before proceeding");
			}
		}
		
        // --------------------- //
        // --- MAGIC METHODS --- //
        // --------------------- //
        public function __get($key) {
            return $this->get($key);
        }

        public function __set($key, $value) {
            $this->set($key, $value);
        }

        public function __isset($key) {
            return isset(self::$data[$key]);
        }
		
		
	}
