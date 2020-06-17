# Decorator implementation details

A *decorator* is responsible for executing multiple adapters at once and can influence the weighting of each adapter.

In our bundle one decorator is predefined called *EqualWeightDecorator*. This decorator weights all adapters the same.

## AbstractDecorator
Since our decorator should be used like an adapter every decorator implements the `AdapterInterface. 

`AbstractDecorator` implements `AdapterInterface` and defines the basic functionality of each decorator. Internally added adapters are stored in an array. Since the method `addAdapter()` returns our decorator this methods are chainable.

Since every decorator implements `AdapterInterface` our abstract class `AbstractDecorator` defines the two methods `addPersonalization()` and `getDebugInfo()`.

`addPersonalization()` executes each adapter to add every adapters personalization to the Elasticsearch query.

`getDebugInfo()` collects segment information of the current personalization from each adapter and returns them.

At last an abstract method `invokeAdapter()` exists which has to be overwritten by a concrete class. This method defines what happens if an adapter gets invoked.

```php
abstract class AbstractDecorator implements AdapterInterface
{
    private $adapters;

    public function __construct(array $adapters = array())
    {
        $this->adapters = $adapters;
    }

    public function addAdapter(AdapterInterface $adapter): AbstractDecorator
    {
        $this->adapters[] = $adapter;
        return $this;
    }

    public function addPersonalization(array $query, float $weight = 1.0, string $boostMode = "multiply"): array
    {
        foreach ($this->adapters as $adapter) {
            $query = $this->invokeAdapter($adapter, $query);
        }
        return $query;
    }

    public function getDebugInfo(float $weight = 1.0, string $boostMode = "multiply"): array
    {
        $res = [];

        foreach ($this->adapters as $adapter) {
            $res[] = $adapter->getDebugInfo($weight, $boostMode);
        }
        return $res;
    }

    abstract protected function invokeAdapter(AdapterInterface $adapter, array $query): array;
}
```

## EqualWeightDecorator
`EqualWeightDecorator` is a class provided by our bundle. This decorator weights all adapters equally.

Therefore we only need to specify the abstract method `invokeAdapter` where we call the `addPersonalization` method of one adapter which is provided by the first argument.

```php
class EqualWeightDecorator extends AbstractDecorator
{
    protected function invokeAdapter(AdapterInterface $adapter, array $query): array
    {
        return $adapter->addPersonalization($query);
    }
}
```
