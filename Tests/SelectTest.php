<?php

namespace Panada\Database\Tests;

class SelectTest extends Connection
{
    public function testSelect()
    {
        $users = self::$db->getAll('account');

        $this->assertGreaterThanOrEqual(2, count($users));
    }
}
