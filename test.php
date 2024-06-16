<?php
$config['host'] = 'localhost';
$config['user'] = 'root';
$config['pass'] = '';
$config['data'] = 'test';

require 'vendor/autoload.php';

use Koyabu\DatabaseConnection\Connection as DatabaseConnect;
$Database = new DatabaseConnect($config);
?>