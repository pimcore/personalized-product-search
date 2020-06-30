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

namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad;

class CustomerInfo
{
    public $customerId;
    public $segments;

    public function __construct($customerId, $segments = [])
    {
        $this->customerId = $customerId;
        $this->segments = $segments;
    }

    public static function __set_state($serializedRepresentation)
    {
        $instance = new self($serializedRepresentation['customerId']);
        foreach ($serializedRepresentation['segments'] as $segment) {
            $instance->segments[] = $segment;
        }

        return $instance;
    }
}
