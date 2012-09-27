<?php

	class MongORM {
		
		
		static $connection;
		static $isSingle;
		static $database;
		static $self;
		static $newData;
		static $instance;
		static $collection;
		static $query = array();
		static $data;
		static $fields = array();
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
		
		public function insert($data = null) {
			self::$data = $data !== null ? $data : array();
			return $this;
		}
		
		public insert_many($data) {
			
		}
		
		public function update() {
			self::$collection->update(self::$data, self::$newData);
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
			self::combineQuery($query);
			self::$data = self::$collection->findOne(self::$query, self::$fields);
			return $this;
		}
		
		public function find_many($query = null) {
			self::combineQuery($query);
			self::$cursor = self::$collection->find(self::$query, self::$fields);
			return $this;
		}
				
		public function combineQuery($query) {
			if(count(self::$query) > 0 && $query) {
				self::$query = array_merge($query, self::$query);
			} else {
				self::$query = $query;
			}
		}
		
		public function getId() {
			return self::$data['_id'];
		}
		
		
		private function get($key) {
			return isset(self::$data[$key]) ? self::$data[$key] : null;
		}
		
		private function set($key, $value) {
			if(!isset(self::$data[$key])) {
				self::$data[$key] = $value;
			} else {
				self::$newData[$key] = $value;				
			}
		} 
		
		static function confirmDatabase() {
			if(!self::$database) {
				Throw new Exception("Please call MongORM::connect() before proceeding");
			}
		}
		
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
