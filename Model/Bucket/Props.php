<?php
namespace Kbrw\RiakBundle\Model\Bucket;

use JMS\Serializer\Annotation as Ser;

/**
 * @Ser\AccessType("public_method")
 * @Ser\XmlRoot("props")
 */
class Props
{
    /**
     * @var string
     * @Ser\Type("string")
     * @Ser\SerializedName("name")
     * @Ser\Since("1")
     */
    protected $name;

    /**
     * @var boolean
     * @Ser\Type("boolean")
     * @Ser\SerializedName("allow_mult")
     * @Ser\Since("1")
     */
    protected $allowMult;

    /**
     * @var boolean
     * @Ser\Type("boolean")
     * @Ser\SerializedName("basic_quorum")
     * @Ser\Since("1")
     */
    protected $basicQuorum;

    /**
     * @var integer
     * @Ser\Type("integer")
     * @Ser\SerializedName("big_vclock")
     * @Ser\Since("1")
     */
    protected $bigVclock;

    /**
     * @var \Kbrw\RiakBundle\Model\Bucket\ErlangCall
     * @Ser\Type("Kbrw\RiakBundle\Model\Bucket\ErlangCall")
     * @Ser\SerializedName("chash_keyfun")
     * @Ser\Since("1")
     */
    protected $chashKeyfun;

    /**
     * @var string
     * @Ser\Type("string")
     * @Ser\SerializedName("dw")
     * @Ser\Since("1")
     */
    protected $dw;

    /**
     * @var boolean
     * @Ser\Type("boolean")
     * @Ser\SerializedName("last_write_wins")
     * @Ser\Since("1")
     */
    protected $lastWriteWins;

    /**
     * @var \Kbrw\RiakBundle\Model\Bucket\ErlangCall
     * @Ser\Type("Kbrw\RiakBundle\Model\Bucket\ErlangCall")
     * @Ser\SerializedName("linkfun")
     * @Ser\Since("1")
     */
    protected $linkfun;

    /**
     * @var integer
     * @Ser\Type("integer")
     * @Ser\SerializedName("n_val")
     * @Ser\Since("1")
     */
    protected $nVal;

    /**
     * @var boolean
     * @Ser\Type("boolean")
     * @Ser\SerializedName("notfound_ok")
     * @Ser\Since("1")
     */
    protected $notfoundOk;

    /**
     * @var integer
     * @Ser\Type("integer")
     * @Ser\SerializedName("old_vclock")
     * @Ser\Since("1")
     */
    protected $oldVclock;

    /**
     * @var array<\Kbrw\RiakBundle\Model\Bucket\ErlangCall>
     * @Ser\Type("array<Kbrw\RiakBundle\Model\Bucket\ErlangCall>")
     * @Ser\SerializedName("postcommit")
     * @Ser\Since("1")
     */
    protected $postcommit;

    /**
     * @var integer
     * @Ser\Type("integer")
     * @Ser\SerializedName("pr")
     * @Ser\Since("1")
     */
    protected $pr;

    /**
     * @var array<\Kbrw\RiakBundle\Model\Bucket\ErlangCall>
     * @Ser\Type("array<Kbrw\RiakBundle\Model\Bucket\ErlangCall>")
     * @Ser\SerializedName("precommit")
     * @Ser\Since("1")
     */
    protected $precommit;

    /**
     * @var integer
     * @Ser\Type("integer")
     * @Ser\SerializedName("pw")
     * @Ser\Since("1")
     */
    protected $pw;

    /**
     * @var string
     * @Ser\Type("string")
     * @Ser\SerializedName("r")
     * @Ser\Since("1")
     */
    protected $r;

    /**
     * @var string
     * @Ser\Type("string")
     * @Ser\SerializedName("rw")
     * @Ser\Since("1")
     */
    protected $rw;

    /**
     * @var integer
     * @Ser\Type("integer")
     * @Ser\SerializedName("small_vclock")
     * @Ser\Since("1")
     */
    protected $smallVclock;

    /**
     * @var string
     * @Ser\Type("string")
     * @Ser\SerializedName("w")
     * @Ser\Since("1")
     */
    protected $w;

    /**
     * @var integer
     * @Ser\Type("integer")
     * @Ser\SerializedName("young_vclock")
     * @Ser\Since("1")
     */
    protected $youngVclock;

    /**
     *
     * @param string                                          $name
     * @param boolean                                         $allowMult
     * @param boolean                                         $basicQuorum
     * @param integet                                         $bigVclock
     * @param \Kbrw\RiakBundle\Model\Bucket\ErlangCall        $chashKeyfun
     * @param string                                          $dw
     * @param boolean                                         $lastWriteWins
     * @param \Kbrw\RiakBundle\Model\Bucket\ErlangCall        $linkfun
     * @param integer                                         $nVal
     * @param boolean                                         $notfoundOk
     * @param integer                                         $oldVclock
     * @param array<\Kbrw\RiakBundle\Model\Bucket\ErlangCall> $postcommit
     * @param integer                                         $pr
     * @param array<\Kbrw\RiakBundle\Model\Bucket\ErlangCall> $precommit
     * @param integer                                         $pw
     * @param string                                          $r
     * @param string                                          $rw
     * @param integer                                         $smallVclock
     * @param string                                          $w
     * @param integer                                         $youngVclock
     */
    public function __construct($name = null, $allowMult = false, $basicQuorum = false, $bigVclock = 50, $chashKeyfun = null, $dw = "quorum", $lastWriteWins = false, $linkfun = null, $nVal = 3, $notfoundOk = true, $oldVclock = 86400, $postcommit = array(), $pr = 0, $precommit = array(), $pw = 0, $r = "quorum", $rw = "quorum", $smallVclock = 50, $w = "quorum", $youngVclock = 20)
    {
        if (!isset($chashKeyfun)) $chashKeyfun = ErlangCall::getDefaultChashKeyFun();
        if (!isset($linkfun))     $linkfun     = ErlangCall::getDefaultLinkFun();

        $this->setName($name);
        $this->setAllowMult($allowMult);
        $this->setBasicQuorum($basicQuorum);
        $this->setBigVclock($bigVclock);
        $this->setChashKeyfun($chashKeyfun);
        $this->setDw($dw);
        $this->setLastWriteWins($lastWriteWins);
        $this->setLinkfun($linkfun);
        $this->setNVal($nVal);
        $this->setNotfoundOk($notfoundOk);
        $this->setOldVclock($oldVclock);
        $this->setPostcommit($postcommit);
        $this->setPr($pr);
        $this->setPrecommit($precommit);
        $this->setPw($pw);
        $this->setR($r);
        $this->setRw($rw);
        $this->setSmallVclock($smallVclock);
        $this->setW($w);
        $this->setYoungVclock($youngVclock);
    }

    public function hasCommitHook($erlangCalls, $mod, $fun)
    {
        foreach ($erlangCalls as $erlangCall) {
            if ($erlangCall->getMod() === $mod && $erlangCall->getFun() === $fun) return true;
        }

        return false;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getAllowMult()
    {
        return $this->allowMult;
    }

    public function setAllowMult($allowMult)
    {
        $this->allowMult = $allowMult;
    }

    public function getBasicQuorum()
    {
        return $this->basicQuorum;
    }

    public function setBasicQuorum($basicQuorum)
    {
        $this->basicQuorum = $basicQuorum;
    }

    public function getBigVclock()
    {
        return $this->bigVclock;
    }

    public function setBigVclock($bigVclock)
    {
        $this->bigVclock = $bigVclock;
    }

    public function getChashKeyfun()
    {
        return $this->chashKeyfun;
    }

    public function setChashKeyfun($chashKeyfun)
    {
        $this->chashKeyfun = $chashKeyfun;
    }

    public function getDw()
    {
        return $this->dw;
    }

    public function setDw($dw)
    {
        $this->dw = $dw;
    }

    public function getLastWriteWins()
    {
        return $this->lastWriteWins;
    }

    public function setLastWriteWins($lastWriteWins)
    {
        $this->lastWriteWins = $lastWriteWins;
    }

    public function getLinkfun()
    {
        return $this->linkfun;
    }

    public function setLinkfun($linkfun)
    {
        $this->linkfun = $linkfun;
    }

    public function getNVal()
    {
        return $this->nVal;
    }

    public function setNVal($nVal)
    {
        $this->nVal = $nVal;
    }

    public function getNotfoundOk()
    {
        return $this->notfoundOk;
    }

    public function setNotfoundOk($notfoundOk)
    {
        $this->notfoundOk = $notfoundOk;
    }

    public function getOldVclock()
    {
        return $this->oldVclock;
    }

    public function setOldVclock($oldVclock)
    {
        $this->oldVclock = $oldVclock;
    }

    /**
     * @return array<\Kbrw\RiakBundle\Model\Bucket\ErlangCall>
     */
    public function getPostcommit()
    {
        return $this->postcommit;
    }

    public function setPostcommit($postcommit)
    {
        $this->postcommit = $postcommit;
    }

    public function addPostcommit($postcommit)
    {
        $this->postcommit[] = $postcommit;
    }

    public function removePostcommit($postcommit)
    {
        $hooks = array();
        foreach ($this->postcommit as $erlangCall) {
            if ($erlangCall->equalTo($postcommit)) {
                $hooks[] = $postcommit;
            }
        }
        $this->postcommit = $hooks;
    }

    public function hasPostCommitHook($erlangCall)
    {
        return $this->hasCommitHook($this->postcommit, $erlangCall->getMod(), $erlangCall->getFun());
    }

    public function getPr()
    {
        return $this->pr;
    }

    public function setPr($pr)
    {
        $this->pr = $pr;
    }

    /**
     * @return array<\Kbrw\RiakBundle\Model\Bucket\ErlangCall>
     */
    public function getPrecommit()
    {
        return $this->precommit;
    }

    public function setPrecommit($precommit)
    {
        $this->precommit = $precommit;
    }

    public function addPrecommit($precommit)
    {
        $this->precommit[] = $precommit;
    }

    public function removePrecommit($precommit)
    {
        $hooks = array();
        foreach ($this->precommit as $erlangCall) {
            if ($erlangCall->equalTo($precommit)) {
                $hooks[] = $precommit;
            }
        }
        $this->precommit = $hooks;
    }

    public function hasPreCommitHook($erlangCall)
    {
        return $this->hasCommitHook($this->precommit, $erlangCall->getMod(), $erlangCall->getFun());
    }

    public function getPw()
    {
        return $this->pw;
    }

    public function setPw($pw)
    {
        $this->pw = $pw;
    }

    public function getR()
    {
        return $this->r;
    }

    public function setR($r)
    {
        $this->r = $r;
    }

    public function getRw()
    {
        return $this->rw;
    }

    public function setRw($rw)
    {
        $this->rw = $rw;
    }

    public function getSmallVclock()
    {
        return $this->smallVclock;
    }

    public function setSmallVclock($smallVclock)
    {
        $this->smallVclock = $smallVclock;
    }

    public function getW()
    {
        return $this->w;
    }

    public function setW($w)
    {
        $this->w = $w;
    }

    public function getYoungVclock()
    {
        return $this->youngVclock;
    }

    public function setYoungVclock($youngVclock)
    {
        $this->youngVclock = $youngVclock;
    }
}
