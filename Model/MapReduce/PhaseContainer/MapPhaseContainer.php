<?php
namespace Kbrw\RiakBundle\Model\MapReduce\PhaseContainer;

use JMS\Serializer\Annotation as Ser;

/**
 * @Ser\AccessType("public_method")
 * @Ser\XmlRoot("map_phase")
 */
class MapPhaseContainer extends PhaseContainer
{
    /**
     * @Ser\SerializedName("map")
     */
    protected $phase;

    public function __construct($phase = null)
    {
        parent::__construct("map", $phase);
    }
}
