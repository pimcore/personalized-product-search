<?php

// tests/PersonalizedSearchBundle/SampleTest.php

namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests;

use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{

    protected function setUp() {
    }

    public function testAdd() {
        $this->assertEquals(15, 10 + 5);
    }

    /**
     * @dataProvider addDataProvider
     */
    public function testAddWithProvider(int $a, int $b, int $expected)
    {
        $this->assertEquals($expected, $a + $b);
    }

    public function addDataProvider(): array
    {
        return [
            [1, 2, 3],
            [10, 5, 15],
            [-5, 5, 0],
            [5, -5, 0],
            [0, 10, 10],
            [-50, -50, -100],
            [-50, 10, -40]
        ];
    }
}
