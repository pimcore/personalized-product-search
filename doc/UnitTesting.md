# Unit testing
For Unit Testing PHPUnit is used.

## Run tests
Execute `src/PersonalizedSearchBundle/vendor/bin/simple-phpunit`

## Test files
- Test files need to be in the following directory inside the PersonalizedSearchBundle: _tests/PersonalizedSearchBundle_
- The file name needs to match _*Test.php_ (example: _SampleTest.php_)

## Unit test guidelines
Taken from [semantictesting.org](https://semantictesting.org/).

The goal of unit tests is to prove that the smallest unit of code behaves exactly as you expect in isolation.

### Objectives
- Implement one or more test cases that causes the unit of code to fail
- Implement one or more test cases that causes the unit of code to pass
- Deliver production code that has at least 70% code coverage

### Strategies
- Assert external resources are not used
- Assert test cases can be executed independent of other test cases
- Assert the result produced by the unit of code
- Assert the state of stateful objects
- Assert interaction between the system under test and its collaborators

## SampleTest
Following example shows what a very simple test file for the `SegmentBasedAdapter` could look like. `$queryRed` represents the original Elasticsearch query whereas `$expectedSegmentBoostedQueryRed` represents the query with the expected boosting. For more informations about mocking classes take a look at the [documentation](https://phpunit.readthedocs.io/en/9.1/test-doubles.html).
```php
namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests\Adapter;

use CustomerManagementFrameworkBundle\Targeting\SegmentTracker;
use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\PersonalizedSearchBundle\Adapter\SegmentAdapter;
use Pimcore\Targeting\VisitorInfoStorage;

class SegmentAdapterTest extends TestCase
{
    public function testSegmentBasedAdapter()
    {
        $queryRed = array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), );

        $expectedSegmentBoostedQueryRed = array ( 'function_score' => array ( 'query' => array ( 'multi_match' => array ( 'query' => 'red', 'type' => 'cross_fields', 'operator' => 'and', 'fields' => array ( 0 => 'attributes.name^4', 1 => 'attributes.name.analyzed', 2 => 'attributes.name.analyzed_ngram', 3 => 'attributes.manufacturer_name^3', 4 => 'attributes.manufacturer_name.analyzed', 5 => 'attributes.manufacturer_name.analyzed_ngram', 6 => 'attributes.color', 7 => 'attributes.carClass', ), ), ), 'functions' => array ( 0 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 860, ), ), 'weight' => 1.0, ), 1 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 966, ), ), 'weight' => 6.0, ), 2 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 967, ), ), 'weight' => 6.0, ), 3 => array ( 'filter' => array ( 'match' => array ( 'relations.segments' => 968, ), ), 'weight' => 6.0, ), ), 'boost_mode' => 'multiply', ), );

        $segmentAdapter = $this->constructSegmentAdapter();
        $actualSegmentBoostedQueryRed = $segmentAdapter->addPersonalization($queryRed);

        self::assertEquals($expectedSegmentBoostedQueryRed, $actualSegmentBoostedQueryRed);
    }

    private function constructSegmentAdapter() : SegmentAdapter
    {
        $visitorInfoStorage = $this
            ->getMockBuilder(VisitorInfoStorage::class)
            ->setMethods(['getVisitorInfo'])
            ->getMock();
        $visitorInfoStorage->method('getVisitorInfo')
            ->willReturn(null);

        $segmentTracker = $this
            ->getMockBuilder(SegmentTracker::class)
            ->setMethods(['getAssignments'])
            ->getMock();
        $segmentTracker->method('getAssignments')
            ->willReturn(array ( 860 => 1, 966 => 6, 967 => 6, 968 => 6, ));

        return new SegmentAdapter($visitorInfoStorage, $segmentTracker);
    }
}
```

## Using PHP Stan
- Run php stan using `src/PersonalizedSearchBundle/vendor/bin/phpstan analyse src tests` to analyze code in the src and tests folder.
- You can currently choose from 9 levels (0 is the loosest and 8 is the strictest) by passing `-l|--level` to the `analyse` command: `vendor/bin/phpstan analyse -l 2 src tests`. Keep in mind that we want to reach PHP Stan level 2.

For further information visit the [PHP Stan documentation](https://phpstan.org/user-guide/command-line-usage).

## Code Coverage Report

In order to generate a code coverage report when running PHP Unit, you can use the following command to generate a static HTML report saved to the directory `/tmp/codeCoverage/`.

```bash
vendor/bin/simple-phpunit --prepend build/xdebug-filter.php --coverage-html /tmp/codeCoverage/
```

You can then view the report using a standard browser.
If you want to view the report directly from the development VM, you can start a simple Python-powered web server to serve the static HTML pages.
The commands for achieving that are listed below.

```bash
# change into the directory in which your code coverage report is saved in
cd /tmp/codeCoverage/

python3 -m http.server 8080
```

You can now access the code coverage report by navigating your browser to `http://<host-ip>:8080`.
