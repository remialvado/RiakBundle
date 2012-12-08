<?php
namespace Kbrw\RiakBundle\Model\Bucket;

use JMS\Serializer\Annotation as Ser;

/** 
 * @Ser\AccessType("public_method") 
 * @Ser\XmlRoot("erlangCall")
 */
class ErlangCall
{
    
    /** 
     * @Ser\Type("string") 
     * @Ser\SerializedName("mod")
     * @Ser\Since("1")
     * @var string
     */
    protected $mod;
    
    /** 
     * @Ser\Type("string") 
     * @Ser\SerializedName("fun")
     * @Ser\Since("1")
     * @var string
     */
    protected $fun;
    
    public static function getDefaultChashKeyFun()
    {
        return new self("riak_core_util", "chash_std_keyfun");
    }
    
    public static function getDefaultLinkFun()
    {
        return new self("riak_kv_wm_link_walker", "mapreduce_linkfun");
    }
    
    public static function getErlangCallUsedToIndexData()
    {
        return new self("riak_search_kv_hook", "precommit");
    }
    
    /**
     * @param string $mod
     * @param string $fun
     */
    function __construct($mod = null, $fun = null) 
    {
        $this->setMod($mod);
        $this->setFun($fun);
    }
    
    /**
     * @return string
     */
    public function getMod()
    {
        return $this->mod;
    }

    /**
     * @param string $mod
     */
    public function setMod($mod)
    {
        $this->mod = $mod;
    }

    /**
     * @return string
     */
    public function getFun()
    {
        return $this->fun;
    }

    /**
     * 
     * @param string $fun
     */
    public function setFun($fun)
    {
        $this->fun = $fun;
    }
    
    /**
     * @param \Kbrw\RiakBundle\Model\Bucket\ErlangCall $erlangCall
     * @return boolean
     */
    public function equalTo($erlangCall) {
        return isset($erlangCall) && 
               $erlangCall instanceof Kbrw\RiakBundle\Model\Bucket\ErlangCall &&
               $erlangCall->getMod() === $this->getMod() &&
               $erlangCall->getFun() === $this->getFun();
    }
}