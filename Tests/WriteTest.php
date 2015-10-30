<?php

namespace Panada\Database\Tests;

class WriteTest extends Connection
{
    public function testWrite()
    {
        $name = time();
        
        $insertId = self::$db->insert('account', [
            'user_name' => $name,
            'email' => $name.'@bar.com',
        ]);
        
        $this->assertGreaterThan(0, $insertId);
        
        $user = self::$db->getOne('account', ['user_name' => $name]);
        
        $this->assertTrue(is_object($user));
        
        $user = self::$db->getOne('account', ['user_name' => $name], ['user_name']);
        
        $this->assertEquals(1, count($user));
        
        $update = self::$db->update('account', ['user_id' => 6], ['user_name' => $name]);
        
        $this->assertEquals(1, $update);
        
        $user = self::$db->getOne('account', ['user_name' => $name], ['user_id']);
        
        $this->assertEquals(6, $user->user_id);
        
        $query = self::$db->delete('account', ['user_id' => 6]);
        
        $this->assertEquals(1, $query);
        
        $user = self::$db->getOne('account', ['user_name' => $name], ['user_id']);
        
        $this->assertFalse($user);
    }
}
