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

### Using the ETL mechanism programatically, via commands and via cron jobs
[ETL mechanism usage](./doc/ETL.md)

## Load test and architecture
A detailed documentation for the architecture is available under the [following link](./doc/Architecture.md). As well you can find detailes for the load tests under [this link](./doc/LoadTesting.md).