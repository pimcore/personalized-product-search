# Adapter implementation details

An *adapter* is responsible for personalizing an *Elasticsearch query*.

Three different implementations are provided:
* *SegmentAdapter*: Adds personalization to resemble the users search behaviour. Products in the search query are weighted based on the users visit count. Every user is assigned segments which resemble products, categories or even groups.
* *PurchaseHistoryAdapter*: Adds personalization to order products by the customers buying behaviour. Therefore we need segments based on all orders a customer made. Currently this adapter simply weights products containing segments based on the extracted segments from the orders.
* *RelevantProductsAdapter*: Adds personalization based on a certain customer group the customer is assigned. Each customer group contains segments. Searched products are ordered based on the segments in the corresponding customer group.

## Adapter interface
The `AdapterInterface` defines two methods:
* `addPersonalization()`: Augments the query with the corresponding personalization code.
* `getDebugInfo()`: Returns information based on the current personalization.

```php
interface AdapterInterface
{
    public function addPersonalization(array $query, float $weight = 1.0, string $boostMode = "multiply"): array;
    public function getDebugInfo(float $weight = 1.0, string $boostMode = "multiply"): array;
}
```

## AbstractAdapter
For common functionality the class `AbstractAdapter` was defined. Since we currently have no functionality which is shared between all adapters this class is currently empty.

```php
abstract class AbstractAdapter implements AdapterInterface
{
}
```

## SegmentAdapter

```php


```

## PurchaseHistoryAdapter

```php


```

## RelevantProductsAdapter

```php


```
