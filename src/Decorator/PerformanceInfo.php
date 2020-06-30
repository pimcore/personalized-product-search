<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

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
        $this->timestamp = date('Y-m-d H:i:s');
    }
}
