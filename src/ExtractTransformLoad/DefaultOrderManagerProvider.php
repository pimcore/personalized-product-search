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

use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderManagerInterface;
use Pimcore\Model\DataObject\ClassDefinition;

class DefaultOrderManagerProvider implements PersonalizationOrderManagerProvider
{
    /**
     * @var Factory
     */
    protected $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function getOrderManager(): OrderManagerInterface
    {
        return $this->factory->getOrderManager();
    }

    public function getCustomerClassId(): string
    {
        $classDefinition = ClassDefinition::getByName('Customer');
        if ($classDefinition) {
            return $classDefinition->getId();
        }

        throw new \Exception("No class definition with name 'customer' found.");
    }
}
