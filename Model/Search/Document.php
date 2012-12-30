<?php
namespace Kbrw\RiakBundle\Model\Search;

use JMS\Serializer\Annotation as Ser;

/**
 * @Ser\AccessType("public_method")
 * @Ser\XmlRoot("doc")
 */
class Document
{

    /**
     * @Ser\Type("array<Kbrw\RiakBundle\Model\Search\String>")
     * @Ser\XmlList(inline = true, entry = "str")
     * @Ser\Since("1")
     */
    protected $strings = array();

    /**
     * @Ser\Exclude
     */
    protected $map = array();

    public function __construct($strings = array())
    {
        $this->strings = $strings;
        $this->setMap();
    }

    /**
     * @Ser\PostDeserialize
     */
    public function setMap()
    {
        foreach ($this->strings as $string) {
            $this->map[$string->getName()] = $string;
        }
    }

    public function getMap()
    {
        return $this->map;
    }

    public function get($key)
    {
        return array_key_exists($key, $this->map) ? $this->map[$key] : null;
    }

    public function getStrings()
    {
        return $this->strings;
    }

    public function setStrings($strings)
    {
        $this->strings = $strings;
    }
}
