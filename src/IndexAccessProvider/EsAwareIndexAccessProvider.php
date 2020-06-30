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

use Elasticsearch\ClientBuilder;

abstract class EsAwareIndexAccessProvider
{
    /**
     * @var \Elasticsearch\Client
     */
    protected $esClient;

    /**
     * @var string
     */
    protected $indexPrefix;

    public function __construct($esHost = [], $indexPrefix = '')
    {
        $this->indexPrefix = $indexPrefix;

        if (class_exists('Elasticsearch\\ClientBuilder')) {
            $this->esClient = ClientBuilder::create()->build();
        }

        $builder = ClientBuilder::create();
        $builder->setHosts($esHost);
        $this->esClient = $builder->build();
    }
}
