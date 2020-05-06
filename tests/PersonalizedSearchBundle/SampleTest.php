<?php

// tests/PersonalizedSearchBundle/SampleTest.php

namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests;

use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\IndexAccessProviderInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\OrderIndexAccessProvider;

class SampleTest extends TestCase
{

    protected function setUp() {
    }

    public function testAdd() {
        $this->assertEquals(15, 10 + 5);
    }

    public function testStub() {
        // Create a stub for the OrderIndexAccessProvider class.
        $stub = $this
            -> getMockBuilder("OrderIndexAccessProvider")
            -> setMethods(['fetchSegments'])
            -> getMock();

        // Configure the stub.
        $stub->method('fetchSegments')
            ->willReturn([
                (object) [
                    "segmentId" => 983,
                    "segmentCount" => 1
                ],
                (object) [
                    "segmentId" => 963,
                    "segmentCount" => 2
                ]
            ]);

        $this->assertSame(1, $stub->fetchSegments(1021)[0]->segmentCount);
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
