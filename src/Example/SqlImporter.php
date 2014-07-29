<?php

/**
 * co3k/web-vuln-example
 *
 * Copyright © 2014 Kousuke Ebihara <kousuke@co3k.org> All Rights Reserved
 *
 * This source code is licensed under the Apache License, Version 2.0. You can get a copy of
 * the license is the LICENSE file which is distributed with this code.
 */

namespace Example;

class SqlImporter
{
    protected $conn;
    protected $path;

    public function __construct($conn, $path)
    {
        $this->conn = $conn;
        $this->path = $path;

        if (!is_file($this->path)) {
            throw new RuntimeException('The specified query file does not exist');
        }
    }

    public function execute()
    {
        $queries = explode(';', file_get_contents($this->path));
        foreach ($queries as $sql) {
            $sql = trim($sql);
            if ($sql === '') {
                continue;
            }

            $this->conn->query($sql);
        }
    }
}
