For Unit Testing we use PHPUnit.

## Setup
- Make sure the _src_ folder is uploaded to your VM
- On your VM, navigate to the PersonalizedSearchBundle: `cd /home/pimcoredemo/www/src/PersonalizedSearchBundle`
- Update dependencies: `composer update`
- Run tests with `vendor/bin/simple-phpunit`

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
Following example shows what a very simple test file could look like. For more informations about mocking classes take a look at the [documentation](https://phpunit.readthedocs.io/en/9.1/test-doubles.html).
```php
namespace Pimcore\Bundle\PersonalizedSearchBundle\Tests;

use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{

    protected function setUp() {
    }

    public function testAdd() {
        $this->assertEquals(15, 10 + 5);
    }

    public function testStub() {
        // Create a stub for the OrderIndexAccessProvider class.
        $stub = $this
            -> getMockBuilder("OrderIndexAccessProvider")
            -> setMethods(['fetchSegments'])
            -> getMock();

        // Configure the stub.
        $stub->method('fetchSegments')
            ->willReturn([
                (object) [
                    "segmentId" => 983,
                    "segmentCount" => 1
                ],
                (object) [
                    "segmentId" => 963,
                    "segmentCount" => 2
                ]
            ]);

        $this->assertSame(1, $stub->fetchSegments(1021)[0]->segmentCount);
    }

    /**
     * @dataProvider addDataProvider
     */
    public function testAddWithProvider(int $a, int $b, int $expected)
    {
        $this->assertEquals($expected, $a + $b);
    }

    public function addDataProvider(): array
    {
        return [
            [1, 2, 3],
            [10, 5, 15],
            [-5, 5, 0],
            [5, -5, 0],
            [0, 10, 10],
            [-50, -50, -100],
            [-50, 10, -40]
        ];
    }
}
```

## Testing in Pimcore
In the Pimcore docs there is a whole [section](https://pimcore.com/docs/5.x/Development_Documentation/Development_Tools_and_Details/Testing.html) about testing Pimcore applications. It also contains a link to [Pimcore tests](https://github.com/pimcore/pimcore/tree/master/tests) which use _Codeception_ for unit testing. _Codeception_ is based on _PHPUnit_ and comes with more advanced features. 

## Selection of a testing framework
In the Pimcore docs it is recommended to start unit testing with simple _PHPUnit_ since it is much easier configure. Therefore we decided to use _PHPUnit_ instead of _Codeception_.

## Using PHP Stan
- On your VM, navigate to the PersonalizedSearchBundle: `cd /home/pimcoredemo/www/src/PersonalizedSearchBundle`
- Update dependencies: `composer update`
- Run php stan using `vendor/bin/phpstan analyse src tests` to analyze code in the src and tests folder.
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

You can now access the code coverage report by navigating your browser to [http://192.168.56.10:8080/](http://192.168.56.10:8080/).
