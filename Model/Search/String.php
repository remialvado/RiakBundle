<?php
namespace Kbrw\RiakBundle\Model\Search;

use JMS\Serializer\Annotation as Ser;

/**
 * @Ser\AccessType("public_method")
 * @Ser\XmlRoot("str")
 */
class String implements SimpleType
{

    /**
     * @Ser\Type("string")
     * @Ser\XmlAttribute
     * @Ser\SerializedName("name")
     * @Ser\Since("1")
     */
    protected $name = null;

    /**
     * @Ser\Type("string")
     * @Ser\XmlValue
     * @Ser\Since("1")
     */
    protected $value = null;

    public function __construct($name, $value)
    {
        $this->setName($name);
        $this->setValue($value);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getValue()
    {
        return $this->trim(value);
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
}
