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

