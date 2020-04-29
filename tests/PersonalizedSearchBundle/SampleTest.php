<?php

// tests/PersonalizedSearchBundle/SampleTest.php

namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests;

use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{
    protected function setUp() {
    }

    public function testAdd()
    {
        $this->assertEquals(15, $this->calculator->add(10, 5));
    }

    public function testAddWithProvider(int $a, int $b, int $expected) {
        $result = $a + $b;
        $this->assertEquals($expected, $result);
    }
}
