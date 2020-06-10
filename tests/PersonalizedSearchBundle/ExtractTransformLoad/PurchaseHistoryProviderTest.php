<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests\ExtractTransformLoad;

use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderManagerInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\SegmentInfo;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Getter\GetterInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerInfo;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\PurchaseHistoryProvider;
use Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider\OrderIndexAccessProvider;


class PurchaseHistoryProviderTest extends TestCase
{
    public function testGetPurchaseHistoryLoggedInUser()
    {
        $expectedPurchaseHistory = CustomerInfo::__set_state(array( 'customerId' => 1021, 'segments' => array ( 0 => SegmentInfo::__set_state(array( 'segmentId' => 983, 'segmentCount' => 1, )), 1 => SegmentInfo::__set_state(array( 'segmentId' => 963, 'segmentCount' => 1, )), 2 => SegmentInfo::__set_state(array( 'segmentId' => 982, 'segmentCount' => 1, )), 3 => SegmentInfo::__set_state(array( 'segmentId' => 971, 'segmentCount' => 1, )), 4 => SegmentInfo::__set_state(array( 'segmentId' => 970, 'segmentCount' => 1, )), ), ));

        $purchaseHistoryProvider = $this->createPurchaseHistoryProvider();
        $userId = 1021;
        $actualPurchaseHistory = $purchaseHistoryProvider->getPurchaseHistory($userId);

        self::assertEquals($expectedPurchaseHistory, $actualPurchaseHistory);
    }

    private function createPurchaseHistoryProvider() : PurchaseHistoryProvider
    {
        $getterInterface = $this
            ->getMockBuilder(GetterInterface::class)
            ->setMethods(['get'])
            ->getMock();
        $segmentMap = [
            "70" => [
                new MockSegment(983),
                new MockSegment(963),
                new MockSegment(982)
            ],
            "3437180724" => [
                new MockSegment(971)
            ],
            "4014459541" => [
                new MockSegment(970)
            ]
        ];
        $getterInterface->method('get')
            ->willReturnCallback(function (MockProduct $product) use ($segmentMap)
            {
                return $segmentMap[$product->getProductNumber()];
            });

        $orderIndexProvider = $this
            ->getMockBuilder(OrderIndexAccessProvider::class)
            ->setMethods(['index'])
            ->getMock();
        $orderIndexProvider->method('index')
            ->willReturn(true);

        $orderManager = $this
            ->getMockBuilder(OrderManagerInterface::class)
            ->setMethods(['createOrderList'])
            ->getMock();
        // see https://stackoverflow.com/questions/15907249/how-can-i-mock-a-class-that-implements-the-iterator-interface-using-phpunit for iteration
        $orderList = $this
            ->getMockBuilder(\Traversable::class)
            ->setMethods(['getQuery', 'joinCustomer', 'rewind', 'current', 'key', 'next', 'valid'])
            ->getMock();
        $orderManager->method('createOrderList')
            ->willReturn($orderList);
        $orderQuery = $this
            ->getMockBuilder('OrderQuery')
            ->setMethods(['where'])
            ->getMock();
        $orderList->method('getQuery')
            ->willReturn($orderQuery);
        $orderQuery->method('where')
            ->willReturn($orderQuery);

        $orderListIterator = new \ArrayIterator(array(
            'resultRow' =>
                new MockOrder(1051, 1051, 1052, array(
                    new MockOrderItem(
                        new MockProduct("70")
                    ),
                    new MockOrderItem(
                        new MockProduct("3437180724")
                    ),
                    new MockOrderItem(
                        new MockProduct("4014459541")
                    )
                )),
        ));
        $orderList
            ->method('rewind')
            ->willReturnCallback(function () use ($orderListIterator): void
            {
                $orderListIterator->rewind();
            });
        $orderList
            ->method('current')
            ->willReturnCallback(function () use ($orderListIterator)
            {
                return $orderListIterator->current();
            });
        $orderList
            ->method('key')
            ->willReturnCallback(function () use ($orderListIterator)
            {
                return $orderListIterator->key();
            });
        $orderList
            ->method('next')
            ->willReturnCallback(function () use ($orderListIterator): void
            {
                $orderListIterator->next();
            });
        $orderList
            ->method('valid')
            ->willReturnCallback(function () use ($orderListIterator): bool
            {
                return $orderListIterator->valid();
            });

        return new PurchaseHistoryProvider($getterInterface, new MockOrderManagerProvider($orderManager), $orderIndexProvider);
    }
}
