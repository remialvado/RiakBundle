<?php

namespace Kbrw\RiakBundle\Service\Content;

use Kbrw\RiakBundle\Model\KV\BaseRiakObject;
use Kbrw\RiakBundle\Model\KV\Data;
use Kbrw\RiakBundle\Model\KV\Link;
use Kbrw\RiakBundle\Model\KV\Transmutable;

/**
 * @author remi
 */
class RiakKVHelper
{
    /**
     * @param string $key
     * @param string $content
     * @param string $contentType
     * @param string $fqcn
     * @param array $infos
     * @param \Guzzle\Http\Message\Header[] $headers
     * @return \Kbrw\RiakBundle\Model\KV\Data
     */
    public function read($key, $content, $contentType, $fqcn, &$infos = array(), $headers = array())
    {
        $data = new Data($key);
        try {
            $data->setStringContent($content);
            $normalizedContentType = $this->contentTypeNormalizer->getNormalizedContentType($contentType);
            if ($this->contentTypeNormalizer->isFormatSupportedForSerialization($normalizedContentType)) {
                $ts = microtime(true);
                $riakKVObject = $this->serializer->deserialize($data->getContent(true), $fqcn, $normalizedContentType);
                $infos["deserialization_time"] = microtime(true) - $ts;
                if ($riakKVObject !== false) {
                    if ($riakKVObject instanceof Transmutable) {
                        $riakKVObject = $riakKVObject->transmute();
                    }
                    if ($riakKVObject instanceof BaseRiakObject) {
                        if (array_key_exists("x-riak-vclock", $headers)) {
                            $vclockHeader = $headers["x-riak-vclock"];
                            $riakKVObject->setRiakVectorClock($vclockHeader->__toString());
                        }
                        if (array_key_exists("link", $headers)) {
                            $linksHeader = $headers["link"];
                            foreach($linksHeader->toArray() as $linkHeader) {
                                $matches = array();
                                if (preg_match('/<(riak/.*/.* )>; riaktag="(.*)"/', $linkHeader, $matches)) {
                                    $riakKVObject->addRiakLink(new Link($matches[1], $matches[2]));
                                }
                            }
                        }
                    }
                    $data->setContent($riakKVObject);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error("Unable to create the Data object for key '$key'. Full message is : \n" . $e->getMessage() . "");
        }
        return $data;
    }

    /**
     * @param \Kbrw\RiakBundle\Service\Content\ContentTypeNormalizer $contentTypeNormalizer
     * @param \JMS\Serializer\Serializer $serializer
     * @param \Psr\Log\LoggerInterface $logger
     */
    function __construct($contentTypeNormalizer, $serializer, $logger)
    {
        $this->contentTypeNormalizer = $contentTypeNormalizer;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }
    
    /**
     * @var \Kbrw\RiakBundle\Service\Content\ContentTypeNormalizer
     */
    public $contentTypeNormalizer;

    /**
     * @var \JMS\Serializer\Serializer
     */
    public $serializer;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;
}
