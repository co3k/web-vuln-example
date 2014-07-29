<?php

/**
 * co3k/web-vuln-example
 *
 * Copyright © 2014 Kousuke Ebihara <kousuke@co3k.org> All Rights Reserved
 *
 * This source code is licensed under the Apache License, Version 2.0. You can get a copy of
 * the license is the LICENSE file which is distributed with this code.
 */

namespace Example\Repository;

abstract class AbstractRepository
{
    protected $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }
}
