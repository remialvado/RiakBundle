<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Transformer;

class FloatToString extends Transformer
{
    public function __construct($parent = null)
    {
        parent::__construct($parent, "float_to_string");
    }
}