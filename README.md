# Personalized Product Search for Pimcore

This bundle adds the ability to customize Elasticsearch queries searching for products using various personalization criteria, such as browsing history, purchase history of a customer and the customer's assignment to customer groups.
The bundle is written in an extensible way so that adding personalization using a custom criteria can easily be achieved.
Furthermore, the bundle is designed to be integrated into existing solutions as easily as possible.

## Provided Functionality in a Nutshell


## Usage
### Install the bundle
* Install the ecommerce bundle
* Install the personalized search bundle like any other pimcore bundle

### Implement necessary interfaces
The personalized search bundles needs some application dependend data to work. Therefore some interfaces are provided which must be implemented. Additionally, those implementations must be available for dependency injection.

In `service.yaml` following entries must be added (keep in mind that the implementations can vary):
```yaml
Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Getter\GetterInterface: '@AppBundle\Ecommerce\IndexService\SegmentGetter'
AppBundle\Personalization\FactoryOrderManagerProvider: ~
Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\PersonalizationOrderManagerProvider: '@AppBundle\Personalization\FactoryOrderManagerProvider'

AppBundle\Personalization\FactoryPersonalizationAdapterCustomerIdProvider: ~
Pimcore\Bundle\PersonalizedSearchBundle\Customer\PersonalizationAdapterCustomerIdProvider: '@AppBundle\Personalization\FactoryPersonalizationAdapterCustomerIdProvider'
```

#### GetterInterface
The `GetterInterface` is part of the ecommerce framework. The implementation should return segments based on the given object which depends on the actual application.

#### PersonalizationOrderManagerProvider
Provides access to the order manager (part of the ecommerce bundle) as well as the customer class id.

```php
namespace AppBundle\Personalization;


use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderManagerInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\PersonalizationOrderManagerProvider;
use Pimcore\Model\DataObject\Customer;

class FactoryOrderManagerProvider implements PersonalizationOrderManagerProvider
{
    function getOrderManager(): OrderManagerInterface
    {
        return Factory::getInstance()->getOrderManager();
    }

    function getCustomerClassId(): string
    {
        return Customer::classId();
    }
}
```

#### PersonalizationAdapterCustomerIdProvider
Used to retrieve the id for the currently logged in user. In the following example functionality provided by the ecommerce framework is used.

```php
namespace AppBundle\Personalization;


use Pimcore\Bundle\PersonalizedSearchBundle\Customer\PersonalizationAdapterCustomerIdProvider;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;

class FactoryPersonalizationAdapterCustomerIdProvider implements PersonalizationAdapterCustomerIdProvider
{
    public function getCustomerId(): int
    {
        return Factory::getInstance()->getEnvironment()->getCurrentUserId();
    }
}
```

### Injecting adapters/decorators
Default adapters (*SegmentAdapter*, *PurchaseHistoryAdapter* and *RelevantProductsAdapter*) and default decorators (*EqualWeightDecorator*, *PerformanceMeasurementDecorator*) that are shipped with the Personalization-Bundle are already available via dependency injection. Just make sure all necessary interfaces are implemented and available as explained above.

### Personalizing queries
Adapters modify existing queries. The following example should give an overview of how decorators and adapters can be used:
```php
$queryKey = 'searchTerm';

//$personalizationDecorator = new PerformanceMeasurementDecorator(new AdapterPerformanceIndexAccessProvider());
$personalizationDecorator = new EqualWeightDecorator();
$personalizationDecorator
    ->addAdapter($relevantProductAdapter)
    ->addAdapter($purchaseHistoryAdapter)
    ->addAdapter($segmentAdapter);
$query = $personalizationDecorator->addPersonalization($query);
$productListing->addQueryCondition($query, $queryKey);

echo '<pre>';
print_r($personalizationDecorator->getDebugInfo());
echo '</pre>';
```
First, the used decorators and adapters need to be injected, which is not shown in the code snippet. Then a decorator is defined to manage the underlying adapters. Those can easily be added to the decorator using the *addAdapter* method. After adding adapters *addPersonalization* can be called for the decorator. It takes the Elasticsearch-query as a parameter. This method returns the modified, personalized, query which can then be executed.

For debug purposes, it might be interesting what the modifications (segments and boosting value for each adapter) looks like. For this case the method *getDebugInfo* exists.

More detailed information can be found [here](./doc/Adapters.md) (adapters) and [here](./doc/Decorators.md) (decorators)

### Using the ETL mechanism
The ETL mechanism can be invoked in 3 ways.

#### Programatically through interfaces which can be injected
Extract purchase history for all customers:

`purchaseHistoryProvider->updateOrderIndexFromOrderDb()`

Or just for a single customer:

`purchaseHistoryProvider->fillOrderIndex(customer)`

Updating the customer-group assignments:

`customerGroupProvider->updateCustomerGroupAndSegmentsIndicesFromOrderDb()`
#### Via executing a command on the command line
`./bin/console personalizedsearch:start-etl ExampleArgument`

The argument *ExampleArgument* is optional. When no argument is given, the ETL is executed for purchase history and relevant products, otherwise only for the given one. The value for the argument can be *PurchaseHistory* or *CustomerGroup*.
#### Via creating cron job entries to automate the invocation
For running the whole ETL every hour, following entry has to be made in the cron tab:

`* */1 * * * /home/pimcoredemo/www/bin/console personalizedsearch:start-etl >> /tmp/personalizedsearch-etl.log`

For more details about the ETL and its usage see [ETL](./doc/ETL.md).

### Implementing a custom adapter
A custom adapter should extend *AbstractAdapter* which implements the *AdapterInterface*. There are two methods to implement: *addPersonalization* and *getDebugInfo*. The goal of both methods is to create information on how certain segments are boosted. In *addPersonalization* this information is used to create so called [function scores](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html) that are added to the query whereas in *getDebugInfo* the boosting information is returned. For detailed information on what such an implementation could look like take a look at the default adapters and the more [detailed adapter documentation](./doc/Adapters.md).

Finally, the adapter should be added to *services.yml* to make it available via dependency injection, just like the default ones. The new entry in the *services.yml* should look something like this:
```yml
AppBundle\Personalization\Adapter\YourAdapter: ~
```

## Detailed documentation
- [Accessing an ES index](./doc/AccessESIndex.md)
- [Adapters](./doc/Adapters.md)
- [Architecture](./doc/)
- [Decorators](./doc/Decorators.md)
- [ETL](./doc/ETL.md)
- [Unit testing](./doc/UnitTesting.md)

## Load test and architecture
A detailed documentation for the architecture is available under the [following link](./doc/Architecture.md). As well you can find detailes for the load tests under [this link](./doc/LoadTesting.md).
