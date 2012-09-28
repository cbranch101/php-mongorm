# PHP MongORM

an ORM manager for Mongo

## Installation

```php
require('php_mongorm.php');
```

Before you can make requests, you need to connect to a database
	
### Connecting Locally
To connect to a local database called test
```php
MongORM::connect('test');
```
    
### Connecting to a remote host
```php	
MongORM::connect('test', 'host_name');
```

## Create Documents

To create documents in MongORM, reference a collection and call create.  If a collection of the name specified doesn't yet exist, it will automatically be created.  In addition, the array passed into create will be updated with the _id object for the new document

```php
$user = array(
	'name' => 'John',
	'age' => 20,
);
	
MongORM::for_collection('users')
	->create($user);
```
$user has now been updated with an _id object

### Create Multiple Documents At Once
```php
$users = array(
	array(
		'name' => 'Fred',
		'age' => 30,
	),
	array(
		'name' => 'John',
		'age' => 20,
	),
);
	
$users = MongORM::for_collection('users')
	->create_many($users);
```

## Read Documents

To read a document, specify the collection and pass in a query array. If no query array is passed, returns all records in the collection

```php
$users = array(
	array(
		'name' => 'Fred',
		'age' => 30,
	),
	array(
		'name' => 'John',
		'age' => 20,
	),
);
	
MongORM::for_collection('users')
	->create_many($users);

$query = array(
	'name' => 'John',
);	

$john = MongORM::for_collection('users')
	->find_one($query)
	->as_array();
```
The above query will result in

```php
$john = array(
	'_id' => {
		'$id' => '1asdfajiia',
	},
	'name' => 'Fred',
	'age' => 30,
);
```
### Reading Multiple Documents

```php
$query = array(
	'age' => 30,
);	

$users_aged_30 = MongORM::for_collection('users')
	->find_many($query)
	->as_array();
```

Would result in

```php
$users_aged_30 = array(
	1000 => array(
		'_id' => {
			'$id' => 1000,
		},
		'name' => 'Fred',
		'age' => 30,
	),
	1001 => array(
		'_id' => {
			'$id' => 1001,
		},
		'name' => 'John',
		'age' => 30,
	),
);
```

## Update Documents

To update a document, find the document you want to update and change the desired value

```php
$query = array('name' => 'John');
$john = MongORM::for_collection('users')
	->find_many($query);

$john->age = 100;
$success = $john->update();
```
If the update was succesful, update returns true;

### Updating Multiple Documents at once

Updating multiple documents is no different than updating single documents

```php
$users = array(
	array(
		'name' => 'Fred',
		'age' => 30,
	),
	array(
		'name' => 'John',
		'age' => 20,
	),
);
	
MongORM::for_collection('users')
	->create_many($users);
	
$all_users = MongORM::for_collection('users')
	->find_many();

$all_users->age = 5;
$all_users->update();
```
Would result in 
```php 
$all_users = array(
	array(
		'name' => 'Fred',
		'age' => 5,
	),
	array(
		'name' => 'John',
		'age' => 5,
	),
);
```
### Updating multiple attributes in a single call

Pass the attributes you want to set as an array

```php
$attributes_to_update = array(
	'name' => 'George'
	'age' => 100,
);
$all_users->set($attributes_to_update);
$all_users->update();
```

## Deleting Documents

Deleting documents works very similarly to finding documents

```php
$query = array('name' => 'John');
MongORM::for_collection('users')
	->delete_one($query);
```
if successful, delete returns true;

### Deleting Multiple Documents

```php
$query = array('age' => 20);
MongORM::for_collection('users')
	->delete_many($query);
```

## Querying
In addition to the standard array queries, there are some custom querying functions you can use

### Select
Only return specific fields, _id is always returned;

```php
$users = array(
	array(
		'name' => 'Steve',
		'age' => 10,
	),
	array(
		'name' => 'Tom',
		'age' => 10,
	),
);

MongORM::for_collection('users')
	->create_many($users);

$results = MongORM::for_collection('users')
	->select('name')
	->find_many()
	->as_array();
```
Results would be
```php
array(
	1 => array(
		'_id' => {
			'$id' => 1,
		},
		'name' => 'Fred',
	),
	2 => array(
		'_id' => {
			'$id' => 2,
		},
		'name' => 'John',
	),
);
```



	




