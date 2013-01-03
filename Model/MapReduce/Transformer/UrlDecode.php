<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Transformer;

class UrlDecode extends Transformer
{
    public function __construct($parent = null)
    {
        parent::__construct($parent, "urldecode");
    }
}
