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

use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;

class Activity extends AbstractRepository
{
    public function create($body, $stamp, $userId)
    {
        $sql = sprintf('INSERT INTO example_activity (user_id, body, stamp, created_at) VALUES (
            %d, "%s", "%s", datetime("now")
        );', (int)$userId, htmlspecialchars($body), $stamp);

        $this->conn->query($sql);

        return (int)$this->conn->fetchColumn('SELECT LAST_INSERT_ROWID();');
    }

    public function findById($id)
    {
        $builder = $this->conn->createQueryBuilder();
        $builder
            ->select('a.*, u.username AS username')
            ->from('example_activity', 'a')
            ->leftJoin('a', 'example_user', 'u', 'a.user_id = u.id')
            ->setMaxResults(1)
            ->where(sprintf('a.id = %d', $id))
        ;

        return $this->conn->fetchAssoc((string)$builder);
    }

    public function fetchItems($maxResults = 20, $page = 1)
    {
        $page = (int)$page ?: 1;

        $builder = $this->conn->createQueryBuilder();
        $builder
            ->select('a.*, u.username AS username')
            ->from('example_activity', 'a')
            ->leftJoin('a', 'example_user', 'u', 'a.user_id = u.id')
            ->orderBy('a.id', 'DESC')
        ;

        $adapter = new DoctrineDbalAdapter($builder, function ($builder) {
            $builder->select('COUNT (a.id) AS total_results')->setMaxResults(1);
        });

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($maxResults);
        $pagerfanta->setCurrentPage($page);

        return $pagerfanta;
    }

    public function listAvailableStamps()
    {
        return [
            'python', 'cake', 'evil', 'gates', 'japan', 'json',
        ];
    }
}
