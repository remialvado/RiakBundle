<?php
namespace Kbrw\RiakBundle\Model\MapReduce\PhaseContainer;

use JMS\Serializer\Annotation as Ser;

/**
 * @Ser\AccessType("public_method")
 * @Ser\XmlRoot("phase")
 */
abstract class PhaseContainer
{
    protected $phase;
    
    /**
     * @Ser\Exclude
     */
    protected $type;

    public function __construct($type = null, $phase = null)
    {
        $this->setPhase($phase);
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Phase
     */
    public function getPhase()
    {
        return $this->phase;
    }

    public function setPhase($phase)
    {
        $this->phase = $phase;
    }
}
