## Cara Install
Menggunakan [Composer]
```
composer require koyabu/database-connection
```

Contoh penggunaan: test.php
```
<?php
// How to use
// Sample
$config['host'] = 'localhost';
$config['user'] = 'root';
$config['pass'] = '';
$config['data'] = 'test';

require 'vendor/autoload.php';

use Koyabu\DatabaseConnection\Connection as DatabaseConnect;
$Database = new DatabaseConnect($config);

/*
$Database->query("INSERT INTO `test` (id,name) VALUES (1,'TEST')");

$QRY = $Database->query("select * from `test`");
$RESULT = $Database->fetch_assoc($QRY);
*/
?>
```
[Composer]: https://getcomposer.org/download/
