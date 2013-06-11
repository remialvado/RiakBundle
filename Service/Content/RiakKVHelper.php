<?php

namespace Kbrw\RiakBundle\Service\Content;

use Kbrw\RiakBundle\Model\KV\Data;
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
     * @return \Kbrw\RiakBundle\Model\KV\Data
     */
    public function read($key, $content, $contentType, $fqcn, &$infos = array())
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
                    $data->setContent($riakKVObject);
                }
            }
        } catch (\Exception $e) {
            $this->logger->err("Unable to create the Data object for key '$key'. Full message is : \n" . $e->getMessage() . "");
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