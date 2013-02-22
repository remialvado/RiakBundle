CHANGELOG
=========

* 1.1.2 (2013-02-22)

 * Composer : change dependency towards jms/serializer-bundle 

* 1.1.1 (2013-02-18)

 * Bugfix : Riak headers are now applied to Data instance when user is using the fetch method

* 1.1.0 (2013-02-17)

 * Feature : add new 'riak:cluster:status' command

* 1.0.7 (2013-02-15)

 * Bugfix : fix argument type in map/reduce query to allow using something that is not a string (an array of string for example)

* 1.0.6 (2013-02-14)

 * Bugfix : ease support of mutliple map or reduce phases as it should have been according to documentation

* 1.0.5 (2013-02-05)

 * Feature : add InMemoryBucket class to ease unitary tests of classes having a Bucket as a dependency. Do not bump minor version since it is only for test purposes.

* 1.0.4 (2013-01-31)

 * Composer : change dependency towards jms/serializer-bundle

* 1.0.3 (2013-01-28)

  * Bugfix : remove debug trace

* 1.0.2 (2013-01-25)

  * Composer : clean up dependencies

* 1.0.1 (2013-01-25)

  * Bugfix : fix the Data::getContents() method that was having an issue with null content

* 1.0.0 (2013-01-05)

  * Initial Release
