<?php

namespace App\tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Users;

class UsersTest extends TestCase
{
    public function testDefault()
    {
        $product = new Users();
        $this->assertSame(1,1);
    }

}