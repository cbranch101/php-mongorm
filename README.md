# FB Request Monkey

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

To read a document, specify the collection and build a query array


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

