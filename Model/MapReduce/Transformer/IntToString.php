<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Transformer;

class IntToString extends Transformer
{
    public function __construct($parent = null)
    {
        parent::__construct($parent, "int_to_string");
    }
}
