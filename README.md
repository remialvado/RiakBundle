* auto-gen TOC:
{:toc}

RiakBundle
==========

RiakBundle is designed to ease interaction with [Riak](http://basho.com/products/riak-overview/) database. It allows developers to focus on
stored objects instead of on communication APIs.

Features
--------

- support for multi-clustering
- configure clusters : protocol, domain, port, maximum supported connections in parallel, buckets list, ...
- allow developers to plus directly some code into Guzzle to : use proxy, log or count all calls, ...
- insert, delete and fetch objects into a bucket with support for parallel calls (curl_multi) 
- fetch, edit and save bucket properties
- list and count keys inside a bucket

Roadmap
-------

- support for Riak search queries
- support for Secondary indexes insert and fetch operations
- support for MapReduce queries for both Javascript and Erlang
- support for extended bucket configuration (n_val, ...)
- performance dashboard added to Sylfony Debug Toolbar

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
"jms/security-extra-bundle": "1.*",
"jms/di-extra-bundle": "1.*",
"kbrw/riak-bundle": "dev-master"
```

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
$bucket = $logCluster->addBucket("log_" . date('Y-m-d'));
bucket->setFullyQualifiedClassName("MyCompany\MyBundle\Model\LogEntry");
bucket->setFormat("json");
```

### Insert or update data into a bucket

Once your bucket is configured, you just have to create an instance of the object you want to store and ask RiakBundle to store it for you. Example :
```php
$backendCluster = $container->get("riak.cluster.backend");
$user = new \MyCompany\MyBundle\Model\User("remi", "remi.alvado@yahoo.fr");
$backendCluster->selectBucket("user")->put(array("remi" => $user));
```

You can even write multiple users at the same time using parallel calls to Riak : 
```php
$backendCluster = $container->get("riak.cluster.backend");
$grenoble = new \MyCompany\MyBundle\Model\City("38000", "Grenoble");
$paris = new \MyCompany\MyBundle\Model\City("75000", "Paris");
$backendCluster->selectBucket("city")->put(
  array(
    "38000" => $grenoble,
    "75000" => $paris
  )
);
```

The same mecanism can be used to update a data : 
```php
$backendCluster = $container->get("riak.cluster.backend");
$paris = $backendCluster->selectBucket("city")->uniq("paris");
$paris->setName("paris intra muros");
$backendCluster->selectBucket("city")->put(array("75000" => $paris));
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
$datas = $backendCluster->selectBucket("city")->fetch(array("paris", "grenoble"));
$cities = $datas->getStructuredObjects(); // $cities will be an array of \MyCompany\MyBundle\Model\City instances

// Using fetch to get one object
$backendCluster = $container->get("riak.cluster.backend");
$datas = $backendCluster->selectBucket("city")->fetch(array("paris"));
$city = $datas->first()->getStructuredContent(); // $city will be a \MyCompany\MyBundle\Model\City instance

// Using uniq to get one object
$backendCluster = $container->get("riak.cluster.backend");
$city = $backendCluster->selectBucket("city")->uniq("paris"); // $city will be a \MyCompany\MyBundle\Model\City instance
```

### Delete data from a bucket

You just have to supply a list of keys that have to been deleted.
Example : 
```php
// delete a list of key/value pairs
$backendCluster = $container->get("riak.cluster.backend");
$backendCluster->selectBucket("city")->delete(array("paris", "grenoble"));

// delete one single key/value pair
$backendCluster = $container->get("riak.cluster.backend");
$backendCluster->selectBucket("city")->delete("paris");
```

### List keys inside a bucket

Riak does not provide an easy way like SQL databases to list all keys inside a bucket. To do so, it has to run over all keys in the cluster. So, this operation can be really long on a big cluster even if you query against a very small bucket.
Moreover, even if RiakBundle uses the "keys=stream" parameter to stream keys from Riak instead of asking Riak to return them all in one response, please keep in mind that PHP might not work well with a multi-million values array.
To display all keys inside a bucket : 
```php
// delete a list of key/value pairs
$backendCluster = $container->get("riak.cluster.backend");
foreach($backendCluster->selectBucket("city")->keys() as $key) {
  echo "$key\n";
}
```

### Count keys inside a bucket

Sometimes, there are too many keys inside a bucket to get them but you might want to count them.
To count all keys inside a bucket : 
```php
// delete a list of key/value pairs
$backendCluster = $container->get("riak.cluster.backend");
echo "'city' bucket contains" . $backendCluster->selectBucket("city")->count() . " key(s)."
```

