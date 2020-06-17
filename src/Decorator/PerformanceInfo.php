<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\Decorator;

class PerformanceInfo
{
    public $adapterName;
    public $elapsedTime;
    public $timestamp;

    public function __construct($adapterName, $elapsedTime)
    {
        $this->adapterName = $adapterName;
        $this->elapsedTime = $elapsedTime;
        $this->timestamp = date("Y-m-d H:i:s");
    }
}
