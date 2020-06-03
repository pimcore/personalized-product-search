<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\Decorator;

class PerformanceInfo
{
    public $adapterName;
    public $elapsedTime;

    public function __construct($adapterName, $elapsedTime)
    {
        $this->adapterName = $adapterName;
        $this->elapsedTime = $elapsedTime;
    }
}
