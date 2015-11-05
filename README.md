# About Panada Database Package

This is A standalone Panada database package.

## Use as Standalone Package

Install via composer

```
composer require panada/database
```

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

$db = new Panada\Database\SQL([
        'dsn' => 'mysql:host=127.0.0.1;dbname=panada;port=3306',
        'username' => 'root',
        'password' => '',
        'options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_PERSISTENT => true
        ]);

$insert = $db->insert('account', [
            'user_name' => 'foo',
            'email' => 'foo@bar.com',
        ]);
```

## Use as Panada Package

```php
<?php

namespace Controller;

class Hello
{
    use \Panada\Resource\Controller;

    public function index()
    {
        $this->db = \Panada\Database\SQL::getInstance();

        $query = $this->db->insert('users', [
            'name' => rand(), 'email' => 'budi@budi.com', 'password' => 'password'
        ]);
    
        $data = $this->db->select()->from('users')->getAll();
    
        return 'status insert: '.var_export($query, true).' data: <pre>'.print_r($data, true).'</pre>';
    }
}
```

If you have more then one db connection, here's the example:

```php
<?php

return [
    'default' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=mydb1;port=3306',
		'username' => 'root',
		'password' => '',
		'options' => [
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_PERSISTENT => true
		]
    ],
    'db2' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=mydb2;port=3307',
		'username' => 'root',
		'password' => '',
		'options' => [
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_PERSISTENT => true
		]
    ],
    'db3' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=mydb3;port=3308',
		'username' => 'root',
		'password' => '',
		'options' => [
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf9',
            PDO::ATTR_PERSISTENT => true
		]
    ]
];
```

Call the db helper:

```php
public function testDB()
{
    $db1 = \Panada\Database\SQL::getInstance();
    $db2 = \Panada\Database\SQL::getInstance('db2');
    $db3 = \Panada\Database\SQL::getInstance('db3');
}
```

## APIs

### Insert

```php
$query = $this->db->insert('tableName', ['name' => 'jhon', 'email' => 'foo@bar.com']);
```

SQL output:

```sql
INSERT INTO tableName (name, email) VALUES ('jhon', 'foo@bar.com')
```

### Update

```php
$query = $this->db->update('tableName', ['name' => 'jhon gmail', 'email' => 'jhon@gmail.com'], ['id' => 6]);
```

SQL output:

```sql
UPDATE tableName SET name = 'budi', email = 'budi@budi.com' WHERE id = 6
```

### Select

```php
$data = $this->db->select()->from('users')->getAll();
```

SQL output:

```sql
SELECT * FROM users
```

```php
$data = $this->db->select('id', 'name')->from('users')->getAll();
// or
$data = $this->db->select(['id', 'name'])->from('users')->getAll();
```

SQL output:

```sql
SELECT id, name FROM users
```

### Select with SQL built in function

```php
$data = $this->db->select('COUNT(*)')->from('users')->getVar();
```

SQL output:

```sql
SELECT COUNT(*) FROM users
```

### Distinct

```php
$data = $this->db->select('name')->distinct()->from('users')->limit(10)->getAll();
```

SQL output:

```sql
SELECT DISTINCT name FROM users LIMIT 10
```

## Run the Test

Go to project root. Run composer install to get PHPUnit Package.

```
composer install
```

Then run the test:

```
./vendor/bin/phpunit
```