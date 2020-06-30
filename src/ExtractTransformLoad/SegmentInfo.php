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

class SegmentInfo
{
    public $segmentId;
    public $segmentCount;

    public function __construct($segmentId, $segmentCount)
    {
        $this->segmentId = $segmentId;
        $this->segmentCount = $segmentCount;
    }

    public static function __set_state($serializedRepresentation)
    {
        return new self(
            $serializedRepresentation['segmentId'],
            $serializedRepresentation['segmentCount']
        );
    }
}
