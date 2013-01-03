<?php
namespace Kbrw\RiakBundle\Model\MapReduce\PhaseContainer;

use JMS\Serializer\Annotation as Ser;

/**
 * @Ser\AccessType("public_method")
 * @Ser\XmlRoot("reduce_phase")
 */
class ReducePhaseContainer extends PhaseContainer
{
    /**
     * @Ser\SerializedName("reduce")
     */
    protected $phase;
    
    public function __construct($phase = null)
    {
        parent::__construct("reduce", $phase);
    }
}