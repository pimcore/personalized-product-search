# Load Testing 

## Gatling

The decision had to be made between [JMeter](https://jmeter.apache.org/) and [Gatling](https://gatling.io/). We decided to use Gatling because it is the more modern tool and it is simpler in usage. Currently we are just using the Open Source Version of Gatling, but it is as well possible to use the Enterprise Version for visualizing the results.

### Setup for Windows

1. Download Open Source Version [here](https://gatling.io/open-source)
2. Unzip folder
3. Set environment variable GATLING_HOME
4. Open Gatling Recorder (tool that generates scala script) with `%GATLING_HOME%\bin\recorder.bat`
5. Open chrome browser and start recording: Developer Tools -> Network -> Enable Preserve Log -> Clear (check that log is empty)
6. Click through the test case in the Chrome browser
7. Create HAR file in chrome browser: Developer Tools -> Network -> Right Click -> Save all as HAR with content
![createhar](CreateHar.png)
8. In Gatling Recorder: Select Recorder Mode HAR File -> Insert path to HAR file -> Click `Start` -> Scala script will be generated in `/user-files/simulations`
9. Clean up Scala script: Delete requests that are not necessary and adapt number of multiple users at once in the last line at `scn.inject(atOnceUsers(10))`
10. Execute `cd %GATLING_HOME%\bin` and then `powershell "gatling.bat | tee output.txt"` -> Select your scala script in cmd -> Press Enter -> Result printed to cmd and in file `output.txt`

### Tests

Tests executed on [Staging Server](https://studienprojekt2020.elements.live/) and local Broser

Test case: 
1. Login as bamm@modern.age
2. Tenant Switch to Elastic Search Tenant
3. Search with Search Term 'beige'
   
4 Requests are executed for each user, and the test has been executed for 50, 100 and 150 users, so 200, 400 and 600 requests are made.

Used Scala Script for Tests: [beige.scala](beige.scala)

## Results

### Point when load on the system gets critical

It was not possible to reach the point where load tests get critical. The problem is the Internet speed of our home internet connections, because it is not possible to have more than round about 20 users at the same time.

### Graph illustrating the time required for the search over the number of simultaneous requests and time

Every simulation has two visualizations. The first one shows the On the first diagram the x axis is divided into 3 ranges for the response times: <800ms, 800ms-1200ms, >1200ms and failed. The second one shows response time with growing percentile.

#### Simulations without adapters
![without](Without.png)

#### Simulations with all adapters
![with](With.png)

##### Only with Segment based adapter
![segment](Segment.png)

##### Only with Purchase History adapter
![purchase](Purchase.png)

##### Only with Relevant Product adapter
![relevant](Relevant.png)
