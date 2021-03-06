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

namespace Pimcore\Bundle\PersonalizedSearchBundle\IndexAccessProvider;

use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroupAssignment;

interface CustomerGroupIndexAccessProviderInterface
{
    /**
     * Returns all existing customer groups
     *
     * @return array of CustomerGroup objects
     */
    public function fetchCustomerGroups(): array;

    /**
     * Creates an entry in the customer group index to assign a customer to a group
     * If the group does not exist, it is created
     *
     * @param CustomerGroupAssignment $customerGroupAssignment
     */
    public function indexCustomerGroupAssignment(CustomerGroupAssignment $customerGroupAssignment);

    /**
     * Return all segment ids of the group the customer belongs to
     *
     * @param int $customerId
     *
     * @return array
     */
    public function fetchSegments(int $customerId): array;

    /**
     * Drops the customer group assignment index
     */
    public function dropCustomerGroupAssignmentIndex();

    /**
     * Drops the customer group index
     */
    public function dropCustomerGroupIndex();

    /**
     * Creates the customer group assignment index
     */
    public function createCustomerGroupAssignmentIndex();

    /**
     * Creates the customer group index
     */
    public function createCustomerGroupIndex();
}
