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

namespace Pimcore\Bundle\PersonalizedSearchBundle\Adapter;

use CustomerManagementFrameworkBundle\SegmentManager\SegmentManagerInterface;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var SegmentManagerInterface
     */
    protected $segmentManager;

    public function __construct(SegmentManagerInterface $segmentManager)
    {
        $this->segmentManager = $segmentManager;
    }

    protected function getSegmentName(int $segmentId): string
    {
        $segment = $this->segmentManager->getSegmentById($segmentId);
        if ($segment) {
            return $segment->getName();
        }

        return '';
    }
}
