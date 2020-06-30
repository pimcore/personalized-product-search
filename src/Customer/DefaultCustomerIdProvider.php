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

namespace Pimcore\Bundle\PersonalizedSearchBundle\Customer;

use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\Security\Core\Security;

class DefaultCustomerIdProvider implements PersonalizationAdapterCustomerIdProvider
{
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getCustomerId(): int
    {
        $user = $this->security->getUser();
        if ($user instanceof AbstractObject) {
            return $user->getId();
        }
        return 0; 
    }
}
