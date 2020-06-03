<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests\ExtractTransformLoad;


class MockOrder
{
    private $orderId;
    private $orderItemId;
    private $id;
    private $items;

    public function __construct(int $id, int $orderId, int $orderItemId, array $items)
    {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->orderItemId = $orderItemId;
        $this->items = $items;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
