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

class User extends AbstractRepository
{
    const PASSWORD_SALT = 'eadbf336174e4bf40e0b2801e66a869627d2fe8dc251782276251c1a52f3c6fc';
    const PASSWORD_ROUND = 1024;

    public function getUser($id)
    {
        $builder = $this->conn->createQueryBuilder();
        $builder
            ->select('u.*')
            ->from('example_user', 'u')
            ->where("u.id = $id")
            ->setMaxResults(1)
        ;

        return $this->conn->fetchAssoc((string)$builder);
    }

    public function login($username, $password)
    {
        $hashedPassword = $this->hashPassword($username, $password);

        $builder = $this->conn->createQueryBuilder();
        $builder
            ->select('u.id')
            ->from('example_user', 'u')
            ->where("username = '$username' AND password = '$hashedPassword'")
            ->setMaxResults(1)
        ;

        return $this->conn->fetchColumn((string)$builder);
    }

    protected function hashPassword($username, $password)
    {
        $result = $username . $password . self::PASSWORD_SALT;

        for ($i = 0; $i < self::PASSWORD_ROUND; $i++) {
            $result = hash('sha512', $result);
        }

        return $result;
    }
}
