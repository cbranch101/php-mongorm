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

## Create

To make requests, build individual actions out of the query you want to make, the access token for that query, and the method.  The actions will be automatically batched.

```php
$user = array(
	'name' => 'John',
	'age' => 20,
);
	
MongORM::for_collection('users')
	->insert($user);
```
$user has now been updated with an _id object

### Single Action
```php

$action = array(
	'query' => 'me/friends',
	'token' => 'ADLFKJSDFS97823987'
	'method' => 'GET',
	'params' => array(
		'param1' => 'test',
	),
);

$results = FB_Request_Monkey::sendOne($action);
```

### FQL 

```php
$action = array(
	'method' => 'GET',
	'query' => 'fql',
	'token' => 'Aasdlkjaslkjsdf',
	'params' => array(
		'q' => 'SELECT uid, name, pic_square FROM user WHERE uid = me()',
	),
);
```

### FQL MultiQuery

This would return all of the names of all of the friends for the current users

```php
$action = array(
	'method' => 'GET',
	'query' => 'fql',
	'token' => 'AAACZAvGW91SwBAAwx0d8DKTpkwkZCXP2yvF5UK2YNPYJVcDThI7HTFImTutxXrJQH2icFSLZBIkwOr4qD0SxUnMD01rFQJYgNZCfpgFh1wZDZD',
	'params' => array(
		'q' => array(
			'query1' => 'SELECT uid2 FROM friend WHERE uid1 = me()',
			'query2' => 'SELECT name FROM user WHERE uid IN (SELECT uid2 FROM #query1)',
		),
	),
);
```



# Labelling / Grouping

If you want the data that's returned to be grouped, add a label parameter to the action.

```php
$actions = array(
	array(
		'query' => 'me/friends',
		'method' => 'GET',
		'token' => 'ADLFKJSDFS97823987'
		'params' => array(
			'param1' => 'test',
		),
		'label' => 'foo',
	),
	array(
		'query' => 'me',
		'method' => 'GET',
		'token' => 'ADLFKJSDFS97823987'
		'params' => array(
			'param1' => 'test',
		),
		'label' = 'bar',
	),
);
$results = FB_Request_Monkey::sendMany($actions);
```
### Labelled Results

```php		
$labelledResults = array(
	'foo' => array(
		//data for this label
	),
	'bar' => array(
		//data for this label
	),
);
```