<?php
use Kbrw\RiakBundle\Model\Bucket\Props;

$object = new Props("retailer");
$object->setR("quorum");
$object->setW("quorum");
$object->setDw("quorum");
$object->setRw("quorum");