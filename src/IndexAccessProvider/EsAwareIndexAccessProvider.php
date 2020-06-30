<?php


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