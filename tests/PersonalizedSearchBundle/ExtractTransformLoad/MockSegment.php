<?php


namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests\ExtractTransformLoad;


class MockSegment
{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
