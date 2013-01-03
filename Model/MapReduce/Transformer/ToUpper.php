<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Transformer;

class ToUpper extends Transformer
{
    public function __construct($parent = null)
    {
        parent::__construct($parent, "to_upper");
    }
}
