<?php
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php';

// Load library classes
include_once $APP_DIR . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR .
		'lib.inc.php';

// create the ProductStorage instance with the absolute URL address
$storage = new ProductStorage($storage_directory, $HOST_URL_PREFIX .
		$storage_url);

// create the ProductIndex instance with the absolute URL address
$index = new ProductIndex($HOST_URL_PREFIX . $CONFIG['EVENT_PATH']);
$index->connect($CONFIG['db_hostname'],
		$CONFIG['db_read_user'],
		$CONFIG['db_read_pass'],
		$CONFIG['db_name']
		);

// reuse pdo connection for fdsn index
$fdsnIndex = new FDSNIndex();
$fdsnIndex->pdo = $index->connection;
