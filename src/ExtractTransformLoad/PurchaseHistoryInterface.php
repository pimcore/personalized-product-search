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

interface PurchaseHistoryInterface
{
    /**
     * Adds a CustomerInfo object to the order history index
     *
     * @param CustomerInfo $customerInfo
     *
     * @return mixed
     */
    public function fillOrderIndex(CustomerInfo $customerInfo);

    /**
     * Creates and updates the order history index
     */
    public function updateOrderIndexFromOrderDb();

    /**
     * Returns the CustomerInfo with the purchase history for a customer
     *
     * @param int $customerId
     *
     * @return CustomerInfo
     */
    public function getPurchaseHistory(int $customerId): CustomerInfo;
}
