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
    ->addAdapter($relevantProductAdapter);
    //->addAdapter($purchaseHistoryAdapter);
    //->addAdapter($segmentAdapter);
$query = $personalizationDecorator->addPersonalization($query);
$productListing->addQueryCondition($query, $queryKey);

echo '<pre>';
print_r($personalizationDecorator->getDebugInfo());
echo '</pre>';
```
First, the used decorators and adapters need to be injected, which is not shown in the code snippet. Then a decorator is defined to manage the underlying adapters. Those can easily be added to the decorator using the *addAdapter* method. After adding adapters *addPersonalization* can be called for the decorator. It takes the Elasticsearch-query as a parameter. This method returns the modified, personalized, query which can then be executed.

For debug purposes, it might be interesting what the modifications (segments and boosting value for each adapter) looks like. For this case the method *getDebugInfo* exists.

### Using the ETL mechanism
To invoke the ETL mechanism, there are three ways:

* Programatically through interfaces which can be injected
* Via executing a command on the command line
* Via creating cron job entries to automate the invocation

For more details see [ETL](./doc/ETL.md).

