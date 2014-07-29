<?php

/**
 * co3k/web-vuln-example
 *
 * Copyright © 2014 Kousuke Ebihara <kousuke@co3k.org> All Rights Reserved
 *
 * This source code is licensed under the Apache License, Version 2.0. You can get a copy of
 * the license is the LICENSE file which is distributed with this code.
 */

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

require_once __DIR__.'/../config.php';

$path = $app['db_path'];
if (file_exists($path)) {
    unlink($path);
}
touch($path);
chmod($path, 0666);

$initializer = new Example\SqlImporter($app['db'], __DIR__.'/../db/sql/setup.sql');
$initializer->execute();
