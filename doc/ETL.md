# ETL mechanism

The **ETL mechanism** is responsible for providing the necessary data in the ES indices to ensure correct search results when using the concrete adapters.

## ETL mechanism for the purchase history adapter

By using the following interface, either the purchase information of all customers or only specific ones can be extracted from the Order Database in order to fill the ES index.

```
interface PurchaseHistoryInterface
{
    public function fillOrderIndex(CustomerInfo $customerInfo);
    public function updateOrderIndexFromOrderDb();
    public function getPurchaseHistory(int $customerId): CustomerInfo;
}
```

In detail, the segments of a customer's purchased products will be saved into the index.

## ETL mechanism for the relevant products adapter

As for purchase history ETL there also exists an interface for the relevant products ETL:

interface CustomerGroupInterface
{
    public function updateCustomerGroupAndSegmentsIndicesFromOrderDb();
}

Yet, there is only the possibility to update the assignments for all customers at once. The goal of the relevant products ETL is to assign customers with similar purchase behavior to the same group. There exist two ES indices, one for the group assignment for each customer and one for the groups itself, which contains the similar segments of all customers in this group.

Our implementation will assign every customer, who has purchased atleast 1 product with segments, to a group. A customer will be assigned to an existing group, if the intersection of their segments exceeds a certain threshold.

### Possible enhancements

Our implemented strategy to creating the customer groups and assigning the customers is very basic, and not good at for example adjusting a group to newly assigned customers. Therefore, as a more enhanced version, a clustering algorithm could be implemented, which finds the suiting customer groups and assignments more effectively.


## Using the ETL mechanism programatically, via commands and via cron jobs
In this section will be described, how to use the ETL mechanism in its different ways.

### Programatically
To invoke the ETL programatically, the interfaces `PurchaseHistoryInterface` and `CustomerGroupInterface` can be used to fill the ES indices.

### Command
To run the **ETL mechanism** from the console, navigate to the folder containing the console

 `/home/pimcoredemo/www` 
 
 and enter 
 
 `./bin/console personalizedsearch:start-etl`.

To explicitly only run the ETL for the **purchase history**, an argument can be provided like that:

`./bin/console personalizedsearch:start-etl PurchaseHistory`

and for the **customer assignment** to its group:

`./bin/console personalizedsearch:start-etl CustomerGroup`


### Cron job

To run the command at an specified interval, you can create a cron job. To do so, open the crontab with `crontab -e` and create a new line in this file.
To run the command every hour, create the following entry:

`* */1 * * * /home/pimcoredemo/www/bin/console personalizedsearch:start-etl >> /tmp/personalizedsearch-etl.log`.