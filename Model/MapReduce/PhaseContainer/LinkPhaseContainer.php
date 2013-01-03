<?php
namespace Kbrw\RiakBundle\Model\MapReduce\PhaseContainer;

use JMS\Serializer\Annotation as Ser;

/**
 * @Ser\AccessType("public_method")
 * @Ser\XmlRoot("link_phase")
 */
class LinkPhaseContainer extends PhaseContainer
{
    /**
     * @Ser\SerializedName("link")
     */
    protected $phase;
    
    public function __construct($phase = null)
    {
        parent::__construct("link", $phase);
    }
}