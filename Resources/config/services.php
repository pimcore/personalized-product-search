<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;
use AppBundle\Adapter\SegmentAdapter;

/*

$container->setDefinition(
    'personalized_search.example',
    new Definition(
        'PersonalizedSearchBundle\Example',
        array(
            new Reference('service_id'),
            "plain_value",
            new Parameter('parameter_name'),
        )
    )
);

*/
