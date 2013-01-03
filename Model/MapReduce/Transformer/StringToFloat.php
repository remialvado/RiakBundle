<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Transformer;

class StringToFloat extends Transformer
{
    public function __construct($parent = null)
    {
        parent::__construct($parent, "string_to_float");
    }
}