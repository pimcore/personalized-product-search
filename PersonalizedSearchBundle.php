<?php

namespace PersonalizedSearchBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class PersonalizedSearchBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    public function getJsPaths()
    {
        return [
            '/bundles/personalizedsearch/js/pimcore/startup.js'
        ];
    }

    protected function getComposerPackageName(): string
    {
        // getVersion() will use this name to read the version from
        // PackageVersions and return a normalized value
        return 'pimcore/personalized-search';
    }

    public function getVersion()
    {
        return '1.0.0';
    }

}
