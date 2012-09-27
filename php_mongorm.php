<?php

	class MongORM {
		
		
		static $connection;
		static $isSingle;
		static $database;
		static $self;
		static $newData = array();
		static $instance;
		static $collection;
		static $query = array();
		static $data;
		static $fields = array();
		static $cursor;
		static $documents = array();
		
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
		
		static function delete_many($condition) {
			self::$collection->remove($condition);
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
			if(self::hasMultipleDocuments()) {
				return self::$documents;
			} else {
				if(count(self::$documents) > 0) {
					return self::$documents[0];
				} else {
					return array();
				}
			}
		}
		
		
		public function create_one($data) {
			self::$collection->insert($data);
			return $data;
		}
		
		public function create_many($data) {
			self::$collection->batchInsert($data);
			return $data;
		}
		
		public function update() {
			if(count(self::$newData) > 0) {
				foreach(self::$documents as $document) {
					$updateCondition = self::getUpdateConditionForDocument($document);
					self::$collection->update($updateCondition, self::getNewObject());
				}
			}
			self::$newData = array();
			return $this;
		}
		
		public function getUpdateConditionForDocument($document) {
			return array('_id' => $document['_id']);
		}
		
		public function getNewObject() {
			return array('$set' => self::$newData);
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
		
		public function set($contentsToSet) {
			foreach($contentsToSet as $keyToSet => $valueToSet) {
				self::setInDocuments($keyToSet, $valueToSet);
			}
		}
		
		public function find_one($query = null) {
			
			// combine the input query with the existing query
			self::combineQuery($query);
			
			// because this is a find one, it returns a document immediately
			$document = self::$collection->findOne(self::$query, self::$fields);
			
			array_push(self::$documents, $document);
			return $this;
		}
		
		public function find_many($query = null) {
			self::combineQuery($query);
			self::$cursor = self::$collection->find(self::$query, self::$fields);
			$documents = iterator_to_array(self::$cursor);
			self::$documents = array_merge($documents, self::$documents);
			return $this;
		}
				
		public function combineQuery($query) {
			if($query) {
				self::$query = array_merge($query, self::$query);
			}
		}
		
		public function getId() {
			return self::$data['_id'];
		}
		
		private function hasMultipleDocuments() {
			return count(self::$documents > 1);
		}
		
		private function get($key) {
			return isset(self::$data[$key]) ? self::$data[$key] : null;
		}
		
		private function setInDocuments($key, $value) {
			foreach(self::$documents as $documentKey => $document) {
				$document[$key] = $value;
				self::$documents[$documentKey] = $document;
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
            $this->setInDocuments($key, $value);
        }

        public function __isset($key) {
            return isset(self::$data[$key]);
        }
		
		
	}
