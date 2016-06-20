<?php

require_once(__DIR__ . '/vendor/nette/neon/src/neon.php');

/**
 *
 *
 * Spouštět pomocí ./phinx.sh nebo pomocí php vendor/bin/phinx
 *
 * Obsah souboru ./phinx.sh:
 * #!/bin/bash
 * php vendor/bin/phinx $@
 *
 *
 */

define ('DSN_REGEX', '/^((?P<driver>\w+):\/\/)?((?P<user>\w+)?(:(?P<password>\w+))?@)?((?P<adapter>\w+):)?((host=(?P<host>[\w-\.]+))|(unix_socket=(?P<socket_file>[\w\/\.]+)))(:(?P<port>\d+))?((;dbname=|\/)(?P<database>[\w\-]+))?$/Uim');

/**
 * Očekává konfigurační soubor se strukturou:
 *      database:
 *          dsn: 'mysql:host=mysql-server;dbname=databaze'
 *          user: 'uzivatel'
 *          password: 'heslo'
 *          options:
 *              lazy: yes
 *
 * Případně pouze:
 *      database:
 *          dsn: 'uzivatel:heslo@mysql:host=mysql-server:3306;dbname=databaze'
 *
 */
$neon = new \Nette\Neon\Neon();

if (file_exists($configFile = __DIR__ . '/app/config/config.local.neon')) {
	$file = file_get_contents($configFile);
} else {
	throw new Nette\Neon\Exception('File \'config.local.neon\' not found in ' . $configFile . '.');
}

$decoded = $neon->decode($file);

$database = isset($decoded['database']) ? $decoded['database'] : ['user' => '', 'password' => '', 'dsn' => 'mysql:host=localhost;dbname=database'];

preg_match(DSN_REGEX, $database['dsn'], $dsn);

return [
	'paths' => [
		'migrations' => 'migrations'
	],
	'environments' => [
		'default_migration_table' => '_phinx_log',
		'default_database' => 'production',
		'production' => [
			'adapter' => !empty($dsn['adapter']) ? $dsn['adapter'] : 'mysql',
			'host' => $dsn['host'],
			'name' => $dsn['database'],
			'user' => !empty($dsn['user']) ? $dsn['user'] : $database['user'],
			'pass' => !empty($dsn['password']) ? $dsn['password'] : $database['password']
		],
	]
];