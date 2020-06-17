# Using the ETL mechanism programatically, via commands and via cron jobs
In this section will be described, how to use the ETL mechanism in its different ways.

## Programatically
To invoke the ETL programatically, the interfaces `PurchaseHistoryInterface` and `CustomerGroupInterface` can be used to fill the ES indices.

## Command
To run the **ETL mechanism** from the console open the path `/home/pimcoredemo/www` and enter `./bin/console personalizedsearch:start-etl`.
To explicitly only run the ETL for the **purchase history**, an argument can be provided like that:

`./bin/console personalizedsearch:start-etl PurchaseHistory`

and for the **customer assignment** to its group:

`./bin/console personalizedsearch:start-etl CustomerGroup`


## Cron job

To run the command at an specified interval, you can create a cron job. To do so, open the crontab with `crontab -e` and create a new line in this file.
To run the command every hour, create the following entry:

`* */1 * * * /home/pimcoredemo/www/bin/console personalizedsearch:start-etl >> /tmp/personalizedsearch-etl.log`.