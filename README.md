[![Build Status](https://travis-ci.org/remialvado/RiakBundle.png?branch=master)](https://travis-ci.org/remialvado/RiakBundle)

Table Of Content
================
- [Features](#features)
- [Roadmap](#roadmap)
- [Dependencies](#dependencies)
- [Installation](#installation)
- [Configuration](#configuration)
- [Basic Usage](#basic-usage)
    - [Accessing a Cluster](#accessing-a-cluster) 
    - [Defining Bucket content](#defining-bucket-content)
    - [Insert or Update Data into a Bucket](#insert-or-update-data-into-a-bucket)
    - [Fetch Data from a Bucket](#fetch-data-from-a-bucket)
    - [Delete Data from a Bucket](#delete-data-from-a-bucket)
    - [List keys inside a Bucket](#list-keys-inside-a-bucket)
    - [Count keys inside a Bucket](#count-keys-inside-a-bucket)
    - [Search items inside a Bucket using Riak Search](#search-items-inside-a-bucket-using-riak-search)
    - [Perform MapReduce request on a Cluster](#perform-mapreduce-request-on-a-cluster)
    - [Exceptions](#exceptions)
- [Extended Usage](#extended-usage)
    - [Load and edit Bucket configuration](#load-and-edit-bucket-configuration)
    - [Enable Automatic Indexation](#enable-automatic-indexation)
    - [Play with Headers](#play-with-headers)
    - [Define your own bucket class](#define-your-own-bucket-class)
- [Admin Tasks](#admin-tasks)
    - [Common Options](#common-options)
    - [List existing buckets](#list-existing-buckets)
    - [Delete all buckets](#delete-all-buckets)
    - [List keys inside a bucket](#list-keys-inside-a-bucket)
    - [Count keys inside a bucket](#count-keys-inside-a-bucket)
    - [Delete all keys inside a bucket](#delete-all-keys-inside-a-bucket)
    - [Delete one key inside a bucket](#delete-one-key-inside-a-bucket)

Features
--------

RiakBundle is designed to ease interaction with [Riak](http://basho.com/products/riak-overview/) database. It allows developers to focus on
stored objects instead of on communication APIs. Supported features are : 

- support for multi-clustering
- configure clusters : protocol, domain, port, maximum supported connections in parallel, buckets list, ...
- allow developers to plus directly some code into Guzzle to : use proxy, log or count all calls, ...
- insert, delete and fetch objects into a bucket with support for parallel calls (curl_multi) 
- fetch, edit and save bucket properties
- list and count keys inside a bucket
- search on a bucket
- perform mapreduce operations through a fluid interface
- console tasks to list and count keys inside a bucket, delete a single key or an entire bucket
- support for custom search parameters (used by custom Riak Search builds)
- support for Bucket inheritance to allow you to define custom services on a bucket

Roadmap
-------

- support for Secondary indexes insert and fetch operations
- support for extended bucket settings (n_val, ...) in YAML configuration
- performance dashboard added to Symfony Debug Toolbar

Dependencies
------------

RiakBundles requires some other bundles / libraries to work : 
- Guzzle : to handle HTTP requests thru curl
- JMSSerializer : to handle serialization operations

Installation
------------

Only installation using composer is supported at the moment but source code could be easily tweaked to be used outside it.
In order to install RiakBundle in a classic symfony project, just update your composer.json and add or edit this lines :  
```javascript
"kbrw/riak-bundle": "1.0.*"
```

You may also need to adjust the "minimum-stability" to "dev" in your composer.json to make this work.

Then, you just need to add some bundle into your app/AppKernel.php : 
```php
new JMS\SerializerBundle\JMSSerializerBundle(),
new Kbrw\RiakBundle\RiakBundle(),
```

Configuration
-------------

For the next sections, let's assume your infrastructure is made of 2 Riak clusters : one used to store your backend objects and one to store all logs.
The backend cluster contains two pre-defined buckets. One to store some users in JSON and one to store some cities in XML.
On the log cluster, you create a new bucket every day(I know it's strange but why not...) so you can't pre-configured buckets in this cluster.
Moreover, you have conducted robustness campaigns and you know that backend cluster is able to support up to 500 parallels connection where your Frontend is never hitted by more than 5 connections in parallels. So you know that you can send 100 backend requests in parallels per frontend requests but no more. If you try to store or fetch or delete more than 100 objects at once, only the first 100 will be processed. Others will have to wait for a slot to opened. RiakBundle will handle this slot mecanism for you.

All configuration can be made on config.yml file under a new ```riak``` namespace. With that in mind, you should define the following configuration : 

Example :
```yaml
riak:
  clusters:
    backend:
      domain: "127.0.0.1"
      port: "8098"
      client_id: "frontend"
      max_parallel_calls: 100
      buckets:
        users:
          fqcn: 'MyCompany\MyBundle\Model\User'
          format: json
        cities:
          fqcn: 'MyCompany\MyBundle\Model\City'
          format: xml
    log:
      domain: "127.0.0.1"
      port: "8099"
      client_id: "frontend"
```

Basic Usage
-----------

### Accessing a cluster

Each cluster becomes a service called "riak.cluster.<clusterName>". In our example, you can access your two clusters using : 
```php
$backendCluster = $container->get("riak.cluster.backend");
$logCluster = $container->get("riak.cluster.log");
```

### Defining bucket content

Riak stores text-based objects so you need to provide a text-based version of your objects. The best practice we recommand is to create an annotated object which represent your model. 
For example, an User class could be implemented this way : 
```php
<?php

namespace MyCompany\MyBundle\Model;

use JMS\Serializer\Annotation as Ser;

/** 
 * @Ser\AccessType("public_method") 
 * @Ser\XmlRoot("user")
 */
class User
{
    /**
     * @Ser\Type("string") 
     * @Ser\SerializedName("id")
     * @var string
     */
    protected $id;
    
    /**
     * @Ser\Type("string") 
     * @Ser\SerializedName("email")
     * @var string
     */
    protected $email;
    
    function __construct($id = null, $email = null)
    {
        $this->setId($id);
        $this->setEmail($email);
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }
}
```

RiakBundle can automatically take care of serialization / deserialization so that you will always work with User instance and never with its text-based (JSON, XML or YAML) reprensentation.
Your model class and the serialization method can be defined into configuration like described above or you can specify it on a bucket instance like this : 
```php
$logCluster = $container->get("riak.cluster.log");
$bucket = $logCluster->getBucket("log_" . date('Y-m-d'));
$bucket->setFullyQualifiedClassName("MyCompany\MyBundle\Model\LogEntry");
$bucket->setFormat("json");
```

### Insert or update data into a bucket

Once your bucket is configured, you just have to create an instance of the object you want to store and ask RiakBundle to store it for you. Example :
```php
$backendCluster = $container->get("riak.cluster.backend");
$user = new \MyCompany\MyBundle\Model\User("remi", "remi.alvado@yahoo.fr");
$backendCluster->getBucket("user")->put(array("remi" => $user));
```

You can even write multiple users at the same time using parallel calls to Riak : 
```php
$backendCluster = $container->get("riak.cluster.backend");
$grenoble = new \MyCompany\MyBundle\Model\City("38000", "Grenoble");
$paris = new \MyCompany\MyBundle\Model\City("75000", "Paris");
$backendCluster->getBucket("city")->put(
  array(
    "38000" => $grenoble,
    "75000" => $paris
  )
);
```

The same mecanism can be used to update a data : 
```php
$backendCluster = $container->get("riak.cluster.backend");
$paris = $backendCluster->getBucket("city")->uniq("paris");
$paris->setName("paris intra muros");
$backendCluster->getBucket("city")->put(array("75000" => $paris));
```

### Fetch data from a bucket

Once you have some data into your bucket, you can start fetching them. As a matter of fact, you can even have no data and start fetching :)
The Bucket class provides two ways to fetch data depending on your needs : 
- get one single data with the ```uniq($key)``` function
- get a list of data with the ```fetch($keys)``` function
Internally, this two methods are doing the same thing but "uniq" will provide you a [\Kbrw\RiakBundle\Model\KV\Data](RiakBundle/blob/master/Model/KV/Data.php) instance while "fetch" will provide you a [\Kbrw\RiakBundle\Model\KV\Datas](RiakBundle/blob/master/Model/KV/Datas.php) one.
[\Kbrw\RiakBundle\Model\KV\Data](RiakBundle/blob/master/Model/KV/Data.php) class lets you access the actual object (User, City, ...) but also the Response headers like VClock, Last-Modified, ...

Example : 
```php
// Using fetch to get multiple objects
$backendCluster = $container->get("riak.cluster.backend");
$datas = $backendCluster->getBucket("city")->fetch(array("paris", "grenoble"));
$cities = $datas->getStructuredObjects(); // $cities will be an array of \MyCompany\MyBundle\Model\City instances

// Using fetch to get one object
$backendCluster = $container->get("riak.cluster.backend");
$datas = $backendCluster->getBucket("city")->fetch(array("paris"));
$city = $datas->first()->getStructuredContent(); // $city will be a \MyCompany\MyBundle\Model\City instance

// Using uniq to get one object
$backendCluster = $container->get("riak.cluster.backend");
$city = $backendCluster->getBucket("city")->uniq("paris"); // $city will be a \MyCompany\MyBundle\Model\City instance
```

### Delete data from a bucket

You just have to supply a list of keys that have to been deleted.
Example : 
```php
// delete a list of key/value pairs
$backendCluster = $container->get("riak.cluster.backend");
$backendCluster->getBucket("city")->delete(array("paris", "grenoble"));

// delete one single key/value pair
$backendCluster = $container->get("riak.cluster.backend");
$backendCluster->getBucket("city")->delete("paris");
```

### List keys inside a bucket

Riak does not provide an easy way like SQL databases to list all keys inside a bucket. To do so, it has to run over all keys in the cluster. So, this operation can be really long on a big cluster even if you query against a very small bucket.
Moreover, even if RiakBundle uses the "keys=stream" parameter to stream keys from Riak instead of asking Riak to return them all in one response, please keep in mind that PHP might not work well with a multi-million values array.
To display all keys inside a bucket : 
```php
// delete a list of key/value pairs
$backendCluster = $container->get("riak.cluster.backend");
foreach($backendCluster->getBucket("city")->keys() as $key) {
  echo "$key\n";
}
```

### Count keys inside a bucket

Sometimes, there are too many keys inside a bucket to get them but you might want to count them.
To count all keys inside a bucket : 
```php
// delete a list of key/value pairs
$backendCluster = $container->get("riak.cluster.backend");
echo "'city' bucket contains" . $backendCluster->getBucket("city")->count() . " key(s)."
```

### List buckets inside a cluster

If you need to list all buckets inside a cluster, you can use the following example : 
```php
$backendCluster = $container->get("riak.cluster.backend");
print_r(backendCluster->bucketNames());
```

### Search items inside a bucket using Riak Search

Riak supports a Solr-like (and -light) search engine. RiakBundle lets you searching for items on every buckets with search feature activated.
Search can be executed with a simple Solr-Like string query or with a fully qualified [\Kbrw\RiakBundle\Model\Search\Query](RiakBundle/blob/master/Model/Search/Query.php) instance.
Examples : 
```php

// with string based query
$backendCluster = $container->get("riak.cluster.backend");
$usersBucket = $backendCluster->getBucket("users");
$response = $usersBucket->search("id:rem*");

// with full Query instance
$backendCluster = $container->get("riak.cluster.backend");
$usersBucket = $backendCluster->getBucket("users");
$query = new \Kbrw\RiakBundle\Model\Search\Query("id:rem*");
$query->addFieldInList("id"); // only look in "id" field for each object stored in the bucket
$query->setRows(5); // return only 5 results
$response = $usersBucket->search($query);
```

### Perform MapReduce request on a cluster

Riak allows developers to execute mapreduce operations an entire cluster. RiakBundle offers the same possibility through a fluent interface.
Mapreduce operations are made of two main parts : 
- select keys the map phase will be executed against. Developers can choose to execute the mapreduce operation against a full bucket, a predefined subset of keys or a filterable set of keys. The three possibilities will be described below.
- define map and reduce phases using javascript functions, erlang module, ... Some possibilities will be defined below but you can dive into the source code or the API to find more.

Generic example : 
```php
$result = $this->cluster->mapReduce()
  ->on("meals")
  ->map('function(riakObject) {...}')
  ->link('meals', 'menu_') // to follow links pointing to any key matching "menu*" on "meals" bucket
  ->reduce('function(riakObject) {...}')
  ->responseShouldBe("\Some\JMS\Serializable\Type")
  ->send();
```

Basic example : 
```php
// count how many times 'pizza' are served, meal by meal on all seasons
$bucket = $this->cluster->getBucket("meals", true);
$bucket->delete($bucket->keys());
$bucket->put(array("summer-1" => "pizza salad pasta meat sushi"));
$bucket->put(array("summer-2" => "pizza pizza pizza pizza pizza"));
$bucket->put(array("winter-1" => "cheese cheese patatoes meat vegetables"));
$bucket->put(array("autumn-1" => "pizza pizza pizza mushroom meat"));
$result = $this->cluster->mapReduce()
  ->on("meals")
  ->map('
      function(riakObject) {   
          var m =  riakObject.values[0].data.match("pizza");
          return  [[riakObject.key, (m ? m.length : 0 )]];
      }    
  ')
  ->responseShouldBe("array<string, string>")
  ->send();
```

Key filter example : 
```php
// count how many times 'pizza' are served, meal by meal only on winter and autumn
// Apply a specific timeout (10sec) as well
$result = $this->cluster->mapReduce()
  ->filter("meals")
    ->tokenize("-", 1)
    ->or()
      ->eq("winter")
      ->eq("autumn")
    ->end()
  ->done()
  ->map('
      function(riakObject) {   
          var m =  riakObject.values[0].data.match("pizza");
          return  [[riakObject.key, (m ? m.length : 0 )]];
      }    
  ')
  ->timeout(10000)
  ->responseShouldBe("array<string, string>")
  ->send();
```

Key filter example : 
```php
// use an erlang function to execute something on meals
$result = $this->cluster->mapReduce()
  ->on("meals")
  ->configureMapPhase()
    ->setLanguage("erlang")
    ->setModule("riak_mapreduce")
    ->setFunction("map_object_value")
  ->done()
  ->responseShouldBe("array<Acme\DemoBundle\Model\Meal>")
  ->send();
```

### Exceptions ###

If riak is unavailable or down, RiakBundle will throw a `RiakUnavailableException`

Advanced Usage
--------------

### Load and Edit Bucket configuration

Riak allows developers to customize some bucket configuration like _nval_, the number of replicas to be store in the cluster for each and every data.
Please have a look at Riak documentation for closer details.

Using RiakBundle, you can easily update bucket configuration. The [\Kbrw\RiakBundle\Model\Bucket\Bucket](RiakBundle/blob/master/Model/Bucket/Bucket.php) class is not only the place used to execute operations on a bucket, it also contains bucket properties that you can manage.
Example : 
```php
$backendCluster = $container->get("riak.cluster.backend");
$users = $backendCluster->addBucket("users", true); // the second parameter will force RiakBundle to fetch properties for this bucket
$users->getProps()->setNVal(5);
$users->save();
```

### Enable automatic indexation

Riak supports automatic indexation of JSON / XML / Erlang datas from Riak KV to Riak Search. This feature needs to be activated on a per-bucket level. RiakBundle lets you easily do that : 

Example : 
```php
$backendCluster = $container->get("riak.cluster.backend");
$usersBucket = $backendCluster->getBucket("users");
$usersBucket->enableSearchIndexing();
$usersBucket->save();
```

### Play with headers

Riak does not only associate an object to a key but also some headers. Some are pre-defined (Last-Modified, X-Riak-Vclock, ...) but you can also put your own custom headers.
As explained above, the ```put($objects)``` method takes an associated array of objects. Thoses objects can either be your own simple representation of the data you want to store in Riak or a [\Kbrw\RiakBundle\Model\KV\Data](RiakBundle/blob/master/Model/KV/Data.php) instance (the same one returned by the fetch and uniq methods).
On this object, you can define your own custom headers by using the headerBag property which is a [\Symfony\Component\HttpFoundation\HeaderBag](../symfony/symfony/blob/master/src/Symfony/Component/HttpFoundation/HeaderBag.php).
Example : 
```php
$remi = new \MyCompany\MyBundle\Model\User("remi", "remi.alvado@yahoo.fr");
$data = new \Kbrw\RiakBundle\Model\KV\Data("remi");
$data->setContent($remi);
$data->getHeaderBag()->setHeader("X-Signup-Date", date('Y-m-d'));

$backendCluster = $container->get("riak.cluster.backend");
$backendCluster->getBucket("users")->put($remi);
```

The same mecanism can be used to handle Riak merge issues with the X-Riak-Vclock header.

### Define your own Bucket class

Sometimes, you might want to define you own Bucket class so that you can override some existing methods, add new ones, ... This can easily be done by hacking the configuration like that : 
```yaml
riak:
  clusters:
    backend:
      domain: "127.0.0.1"
      port: "8098"
      client_id: "frontend"
      max_parallel_calls: 100
      buckets:
        points_of_interests:
          fqcn: 'MyCompany\MyBundle\Model\PointOfInterest'
          format: json
          class: 'Acme\DemoBundle\Model\AcmeBucket'
```

With this configuration, the "points_of_interests" bucket will be initialized as a ```Acme\DemoBundle\Model\AcmeBucket``` and not a regular Bucket. You can now implement this class (that you extends ```\Kbrw\RiakBundle\Model\Bucket\Bucket```) to atch your requirements.
In the same spirit, each time a Bucket is added to a Cluster, an event named "riak.bucket.add" is thrown to the EventDispatcher. This ```\Symfony\Component\EventDispatcher\GenericEvent``` contains the newly created bucket that you can manipulate the way you want.
For closer details, you can have a look at [this example](https://gist.github.com/4363553).

Admin Tasks
-----------

### Common Options

All tasks working on a cluster (so... all tasks) support the following option :
- ```-c``` or ```--cluster``` : the cluster name. If not provided, it will be asked on the command line

All tasks working on a bucket support the following option : 
- ```-b``` or ```--bucket``` : the bucket name. If not provided, it will be asked on the command line

All tasks which list or count items support the following option : 
- ```-r``` or ```--raw``` : if provided, a raw output format will be used. Mainly used for bash scripting.

All tasks which delete items support the following option : 
- ```-y``` or ```--yes``` : if provided, all yes/no questions will be skipped. mainly used for bash scripting.

### List existing buckets

```bash
php app/console riak:cluster:list -c backend
```

### Delete all buckets

```bash
php app/console riak:cluster:deleteAll -c backend
```

### List keys inside a bucket

```bash
php app/console riak:bucket:list -c backend -b meals
```

### Count keys inside a bucket

```bash
php app/console riak:bucket:count -c backend -b meals
```

### Delete all keys inside a bucket

```bash
php app/console riak:bucket:deleteAll -c backend -b meals
```

### Delete one key inside a bucket

```bash
php app/console riak:bucket:delete -c backend -b meals -k summer-2
```
