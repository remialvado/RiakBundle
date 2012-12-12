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

All configuration can be made on config.yml file under a new ```riak``` namespace. With that in mind, you should define the following configuration : 

Example :
```yaml
riak:
  clusters:
    backend:
      domain: "127.0.0.1"
      port: "8098"
      client_id: "frontend"
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

Usage
-----

## Accessing a cluster

Each cluster becomes a service called "riak.cluster.<clusterName>". In our example, you can access your two clusters using : 
```php
$backendCluster = $container->get("riak.cluster.backend");
$logCluster = $container->get("riak.cluster.log");
```

## Defining bucket content

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

## Insert data into a bucket