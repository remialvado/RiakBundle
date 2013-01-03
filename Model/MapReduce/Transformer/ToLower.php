<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Transformer;

class ToLower extends Transformer
{
    public function __construct($parent = null)
    {
        parent::__construct($parent, "to_lower");
    }
}
