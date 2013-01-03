<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Predicate;

class MemberOf extends Predicate
{
    public function __construct($parent = null, $set = array())
    {
        parent::__construct($parent, "set_member", $set);
    }
}
