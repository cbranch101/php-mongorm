<?php

	class MongORM {
		
		/**
		 * connection
		 *
		 * The connection to a mongo database
		 * 
		 * @var object
		 * @access public
		 * @static
		 */
		static $connection;
		
		/**
		 * database
		 *
		 * The chosen database to connect to
		 * 
		 * @var object
		 * @access public
		 * @static
		 */
		static $database;
		
		/**
		 * new_data
		 * 
		 * (default value: array())
		 * When updating documents, new keys and values that are being
		 * added to a document need to be stored separately so 
		 * they can be passed in in a separate argument
		 * newData holds this data
		 * 
		 * @var array
		 * @access public
		 * @static
		 */
		static $new_data = array();
		
		/**
		 * collection
		 *
		 * The mongo collection to perform CRUD on
		 * 
		 * @var object
		 * @access public
		 * @static
		 */
		static $collection;
		
		/**
		 * query
		 * 
		 * (default value: array())
		 * Holds all of the key value pairs of the current query
		 * 
		 * @var array
		 * @access public
		 * @static
		 */
		static $query = array();
		
		/**
		 * fields
		 * 
		 * (default value: array())
		 * Holds all of the fields that are going to be selected in a query
		 * Used in conjunction with select
		 * 
		 * @var array
		 * @access public
		 * @static
		 */
		static $fields = array();
				
		/**
		 * cursor
		 * 
		 * The object created by the PHP Mongo wrapped that is used
		 * to iterate over results and perform transformations
		 * 
		 * @var object
		 * @access public
		 * @static
		 */
		static $cursor;
		
		/**
		 * documents
		 * 
		 * (default value: array())
		 * The contents of a collection in array format.  Used in the updating process
		 * and when output contents in as_array
		 * 
		 * @var array
		 * @access public
		 * @static
		 */
		static $documents = array();
		
		/**
		 * cursorFunctions
		 *
		 * All of the supported functions that would normally 
		 * be called on the cursor object in the the PHP Mongo wrapper
		 * but that are going to be passed along by MongORM 
		 * through the __call magic method
		 * 
		 * @var mixed
		 * @access public
		 * @static
		 */
		static $cursor_functions = array(
			'sort' => 'sort',
			'limit' => 'limit',
		);
		
		static $query_functions = array(
			'where',
			'where_js', 
		); 
		
		/**
		 * connect function.
		 * 
		 * Open a mongo connection
		 * If no host is specified, the connection defaults to localhost
		 * 
		 * @access public
		 * @static
		 * @param mixed $databaseName
		 * @param mixed $host (default: null)
		 * @return void
		 */
		static function connect($database_name, $host = null) {
			
			// passing a null host into a Mongo connection generates an error
			// make sure that we have a host before passing it in
			self::$connection = $host ? new Mongo($host) : new Mongo();	
			self::$database = self::$connection->$database_name;
		}
		
		/**
		 * delete_one function.
		 * 
		 * Delete a single document, returns true if successful
		 * 
		 * @access public
		 * @static
		 * @param array $condition (default: array()) the conditions for finding the document to delete
		 * @return boolean
		 */
		static function delete_one($condition = array()) {
			
			// set the options to ensure that PHP Mongo only
			// returns one document
			$options = array(
				'justOne' => true,
			);
			return self::$collection->remove($condition, $options);
		}
		
		/**
		 * delete_many function.
		 * 
		 * Deletes all documents that match the condition
		 * If no condition is specified, delete all documents in collection
		 * 
		 * @access public
		 * @static
		 * @param array $condition (default: array())
		 * @return boolean
		 */
		static function delete_many($condition = array()) {
			return self::$collection->remove($condition);
		}
				
		/**
		 * for_collection function.
		 *
		 * Selects the collection to be used
		 * This is the first function called in any chain
		 * 
		 * @access public
		 * @static
		 * @param string $collectionName
		 * @return self
		 */
		static function for_collection($collection_name) {
			self::reset_variables();
			self::confirm_database();
			self::$collection = self::$database->$collection_name;
			return new self;
		}
		
		static function reset_variables() {
			self::$documents = array();
			self::$new_data = array();
			self::$query = array();
			self::$fields = array();
			self::$cursor = null;
		}
		
		/**
		 * as_array function.
		 * 
		 * Returns the documents in the currently collection
		 * If there's only one document, returns that document
		 * If there are multiple, returns an array of documents
		 * 
		 * @access public
		 * @return array
		 */
		public function as_array() {
			
			// if the cursor hasn't been converted to an array
			if(count(self::$documents) == 0) {
				
				// convert it to an array
				self::set_documents_from_cursor();
			}
			
			if(self::has_multiple_documents()) {
				return self::$documents;
			} else {
				
				// if there aren't any documents
				// return an empty array
				if(count(self::$documents) > 0) {
					return self::$documents[0];
				} else {
					return array();
				}
			}
		}
		
		/**
		 * create_one function.
		 * 
		 * Create a new document, return the updated document with an id
		 * 
		 * @access public
		 * @param document $document
		 * @return array a single document
		 */
		public function create_one($document) {
			self::$collection->insert($document);
			return $document;
		}
		
		/**
		 * create_many function.
		 * 
		 * Create multiple new documents, returns those updated docments
		 * 
		 * @access public
		 * @param array $documents
		 * @return array an array of documents
		 */
		public function create_many($documents) {
			self::$collection->batchInsert($documents);
			return $documents;
		}
		
		/**
		 * update function
		 * 
		 * Updates all of the currently selected documents
		 * 
		 * @access public
		 * @return object
		 */
		public function update() {
			if(count(self::$new_data) > 0) {
				foreach(self::$documents as $document) {
					$update_condition = self::get_update_condition_for_document($document);
					self::$collection->update($update_condition, self::get_new_object());
				}
			}
			self::$new_data = array();
			return $this;
		}
		
		/**
		 * get_update_condition_for_document function.
		 * 
		 * Gets the conditions to be passed into the update function
		 * 
		 * @access public
		 * @param array $document
		 * @return array condition
		 */
		public function get_update_condition_for_document($document) {
			return array('_id' => $document['_id']);
		}
		
		/**
		 * get_new_object function.
		 * Gets the new object that needs to be passed in 
		 * as an argument into the update function
		 * 
		 * @access public
		 * @return array
		 */
		public function get_new_object() {
			return array('$set' => self::$new_data);
		}
				
		/**
		 * select function.
		 * 
		 * Specifies fields to return in a query
		 * 
		 * @access public
		 * @param array $fields_to_select
		 * @return void
		 */
		public function select($fields_to_select) {
			if(is_array($fields_to_select)) {
				foreach($fields_to_select as $field) {
					self::$fields[$field] = true;
				}
			} else {
				self::$fields[$fields_to_select] = true;
			}
			return $this;
		}
		
		/**
		 * ensure_index function.
		 * 
		 * A wrapper for the ensureIndex function in PHP Mongo wrapper 
		 * 
		 * @access public
		 * @param array $indexes
		 * @param array $options (default: array())
		 * @return object
		 */
		public function ensure_index($indexes, $options = array()) {
			self::$collection->deleteIndexes();
			self::$collection->ensureIndex($indexes, $options);
			return $this;
		}
		
		/**
		 * delete_indexes function.
		 * 
		 * Removes any existing ensure_indexes
		 * 
		 * @access public
		 * @return void
		 */
		public function delete_indexes() {
			self::$collection->deleteIndexes();
		} 
				
		/**
		 * cursor_function function.
		 * 
		 * Calls functions on the cursor
		 * 
		 * @access public
		 * @param string $function_name
		 * @param array $args
		 * @return void
		 */
		public function cursor_function($function_name, $args) {
			call_user_func_array(array(self::$cursor, $function_name), $args);
			return $this;
		}
		
		public function query_function($function_name, $args) {
			call_user_func_array(array($this, $function_name), $args);
			return $this;
		}
		
		static function query_where($field, $value) {
			self::$query[$field] = $value;
		}
		
		/**
		 * set_documents_from_cursor function.
		 * 
		 * Gets documents out of the cursor as an array
		 * Sets the documents variable
		 * 
		 * @access public
		 * @return array
		 */
		public function set_documents_from_cursor() {
			self::$documents = iterator_to_array(self::$cursor);
		}
		
		/**
		 * set function.
		 * 
		 * Sets new values and keys in documents
		 * When you want to set more than one key at time
		 * 
		 * @access public
		 * @param $contents_to_set
		 * @return void
		 */
		public function set($contents_to_set) {
			foreach($contents_to_set as $key_to_set => $value_to_set) {
				self::set_in_documents($key_to_set, $value_to_set);
			}
		}
		
		/**
		 * has_contents function.
		 * 
		 * Check if the results of a query has any contents
		 * 
		 * @access public
		 * @return void
		 */
		public function has_contents() {
			return self::$cursor->hasNext();
		}
		
		/**
		 * id_exists function.
		 * 
		 * Checks if an ID exists in the collection
		 * 
		 * @access public
		 * @param mixed $id
		 * @return boolean
		 */
		public function id_exists($id) {
			return self::find_by_id($id)
				->has_contents();
		}
				
		/**
		 * as_cursor function.
		 * 
		 * Get the cursor for the current collection
		 * 
		 * @access public
		 * @return void
		 */
		public function as_cursor() {
			return self::$cursor;
		}
		
		/**
		 * find_one function.
		 * 
		 * Find a single document, return the instance for chaining
		 * 
		 * @access public
		 * @param array $query (default: null)
		 * @return object
		 */
		public function find_one($query = null) {
			
			// combine the input query with the existing query
			self::combine_query($query);
			
			// because this is a find one, it returns a document immediately
			$document = self::$collection->findOne(self::$query, self::$fields);
			
			// add the document inside of an array so it can processed later on
			array_push(self::$documents, $document);
			return $this;
		}
		
		/**
		 * find_many function.
		 * 
		 * Return multiple documents
		 * 
		 * @access public
		 * @param array $query (default: null)
		 * @return object
		 */
		public function find_many($query = null) {
			// combine the input query with the existing query
			self::combine_query($query);
			// set the cursor from the collection
			self::$cursor = self::$collection->find(self::$query, self::$fields);
			return $this;
		}
		
		/**
		 * find_by_id function.
		 * 
		 * Find a single document by id
		 * 
		 * @access public
		 * @param mixed $id
		 * @return void
		 */
		public function find_by_id($id) {
			self::find_many(array('_id' => $id));
			return $this;
		}
								
		/**
		 * combine_query function.
		 * 
		 * If any queries have been added up to this point and a custom query
		 * is passed in, combine the two
		 * 
		 * @access public
		 * @param array $query
		 * @return void
		 */
		public function combine_query($query) {
			if($query != null) {
				if(count(self::$query) > 0) {
					self::$query = array_merge(self::$query, $query);
				} else {
					self::$query = $query;
				}
			}
		}
		
		/**
		 * has_multiple_documents function.
		 * 
		 * @access private
		 * @return void
		 */
		private function has_multiple_documents() {
			return count(self::$documents > 1);
		}
		
		/**
		 * get function.
		 * 
		 * Allows $value = $document->key usage
		 * If there are multiple documents, returns an array
		 * 
		 * @access private
		 * @param string $key
		 * @return mixed
		 */
		private function get($key) {
			if(count(self::$documents) < 2) {
				if(count(self::$documents) == 0) {
					return null;
				} else {
					return $documents[0][$key];
				}
			} else {
				$values = array();
				foreach($documents as $document) {
					array_push($values, $document[$key]);
				}
				return $values;
			}			
		}
		
		/**
		 * set_in_documents function.
		 * 
		 * Allows $documents->key = value usage and
		 * $document->key = value usage
		 * 
		 * @access private
		 * @param mixed $key
		 * @param mixed $value
		 * @return void
		 */
		private function set_in_documents($key, $value) {
			if($key != '_id') {
				if(count(self::$documents) == 0) {
					self::set_documents_from_cursor();
				}
				foreach(self::$documents as $document_key => $document) {
					$document[$key] = $value;
					self::$documents[$document_key] = $document;
					self::$new_data[$key] = $value;
				}
			}
		} 
		
		/**
		 * confirm_database function.
		 * 
		 * Makes sure a database connection has been opened before allowing querying
		 * and what not
		 * 
		 * @access public
		 * @static
		 * @return void
		 */
		static function confirm_database() {
			if(!self::$database) {
				Throw new Exception("Please call MongORM::connect() before proceeding");
			}
		}
		
        // Magic Methods
        
        public function __get($key) {
            return $this->get($key);
        }

        public function __set($key, $value) {
            $this->set_in_documents($key, $value);
        }

        public function __isset($key) {
            return isset(self::$data[$key]);
        }
        
        public function __call($method, $args) {
	       $methodFound = false;
	       	
	       	// if the method being called is a cursor function
	       	// call it on a cursor
	        if(isset(self::$cursor_functions[$method])) {
		    	return self::cursor_function(self::$cursor_functions[$method], $args);
	        }
	        if(in_array($method, self::$query_functions)) {
		    	$fullMethod = 'query_' . $method;
		    	return self::query_function($fullMethod, $args);	    
	        }
	        
	        throw new Exception("Method $method not found");        
	    }
	
	}
