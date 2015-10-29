<?php

namespace Panada\Database\Tests;

class InsertTest extends Connection
{
    public function testInsert()
    {
        $name = time();

        $insert = self::$db->insert('account', [
            'user_name' => $name,
            'email' => $name.'@bar.com',
        ]);

        $this->assertEquals(1, $insert);
    }
}
