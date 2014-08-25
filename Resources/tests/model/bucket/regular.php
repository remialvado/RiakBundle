<?php
use Kbrw\RiakBundle\Model\Bucket\Bucket;

$object = new Bucket("retailer");
$object->getProps()->setR("quorum");
$object->getProps()->setW("quorum");
$object->getProps()->setDw("quorum");
$object->getProps()->setRw("quorum");