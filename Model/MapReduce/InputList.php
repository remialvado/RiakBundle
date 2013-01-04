<?php
namespace Kbrw\RiakBundle\Model\MapReduce;

class InputList
{
    /**
     * @var array<\Kbrw\RiakBundle\Model\MapReduce\Input>
     */
    protected $inputs;

    public function __construct($inputs = array())
    {
        $this->setInputs($inputs);
    }

    public function getInputs()
    {
        return $this->inputs;
    }

    public function setInputs($inputs)
    {
        $this->inputs = $inputs;
    }

    public function addInput($input)
    {
        $this->inputs[] = $input;
    }

    public function toArray()
    {
        $content = array();
        foreach ($this->inputs as $input) {
            $content[] = $input->toArray();
        }

        return $content;
    }

    public function getMainBucket()
    {
        return reset($this->inputs)->getBucket();
    }

    public function isDefined()
    {
        return count($this->inputs) > 0;
    }

    public function onlyOneBucketSelected()
    {
        return count($this->inputs) === 1 && reset($this->inputs)->onlyBucketIsDefined();
    }
}
