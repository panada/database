<?php

namespace Panada\Database\Tests;

class SelectTest extends Connection
{
    public function testSelect()
    {
        $users = self::$db->getAll('account');

        $this->assertGreaterThanOrEqual(2, count($users));
        
        $users1 = self::$db->select()->from('account')->getAll();
        
        $this->assertEquals($users1, $users);
        
        $data1 = self::$db->select('user_id', 'user_name')->from('account')->getAll();
        
        $this->assertGreaterThanOrEqual(2, count($data1));
        
        $this->assertObjectHasAttribute('user_name', $data1[0]);

        $data2 = self::$db->select(['user_id', 'user_name'])->from('account')->getAll();
        
        $this->assertEquals($data1, $data2);
        
        $data = self::$db->select('COUNT(*)')->from('account')->getVar();
        
        $this->assertGreaterThanOrEqual(2, (int)$data);
        
        $data = self::$db->select('user_name')->distinct()->from('account')->limit(10)->getAll();
        
        $this->assertEquals(2, count($data));
    }
    
    public function testJoin()
    {
        self::$db->select('account.user_name', 'post.comments')->from('account')->join('post')->on('account.user_id', '=', 'post.author_id')->getAll();
        
        $sql = "SELECT account.user_name, post.comments FROM account JOIN post ON (account.user_id = post.author_id)";
        
        $this->assertEquals($sql, self::$db->getLastQuery());
        
        // RIGHT and FULL OUTER JOINs are not currently supported in sqlite
        try {
            self::$db->select('account.user_name', 'post.comments')->from('account')->join('post', 'RIGHT')->on('account.user_id', '=', 'post.author_id')->getAll();
        }
        catch(\Exception $e) {
            $sql = "SELECT account.user_name, post.comments FROM account RIGHT JOIN post ON (account.user_id = post.author_id)";
            
            $this->assertEquals($sql, self::$db->getLastQuery());
        }
        
        
    }
}
