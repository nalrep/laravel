<?php

namespace Narlrep\Tests;

use Narlrep\Query\Validation;

class ValidationTest extends TestCase
{
    public function test_it_validates_safe_builder_query()
    {
        $validation = new Validation();
        $query = "User::where('active', 1)->get();";
        
        $this->assertTrue($validation->validate($query, 'builder'));
    }

    public function test_it_rejects_destructive_builder_query()
    {
        $validation = new Validation();
        $query = "User::where('id', 1)->delete();";
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Destructive method 'delete' is not allowed.");
        
        $validation->validate($query, 'builder');
    }

    public function test_it_validates_safe_sql_query()
    {
        $validation = new Validation();
        $query = "SELECT * FROM users";
        
        $this->assertTrue($validation->validate($query, 'sql'));
    }

    public function test_it_rejects_destructive_sql_query()
    {
        $validation = new Validation();
        $query = "DELETE FROM users";
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Destructive SQL command 'DELETE' is not allowed.");
        
        $validation->validate($query, 'sql');
    }
}
