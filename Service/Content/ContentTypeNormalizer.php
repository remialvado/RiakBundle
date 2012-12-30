<?php

namespace Kbrw\RiakBundle\Service\Content;

/**
 * @author remi
 */
class ContentTypeNormalizer
{

    /**
     * @var array<string,array<string>>
     * Example :
     *   -> text/csv => [csv]
     *   -> application/xml => [xml, xsd, xsl]
     */
    protected $contentTypes;

    public function __construct($contentTypes = array())
    {
        $this->setContentTypes($contentTypes);
    }

    public function getNormalizedContentType($contentType, $default = null)
    {
        if (is_array($this->contentTypes) && array_key_exists($contentType, $this->contentTypes))
                return $this->contentTypes[$contentType][0];
        return $default;
    }

    public function getContentType($normalizedContentType, $default = "text/plain")
    {
        foreach ($this->contentTypes as $contentType => $normalizedContentTypes) {
            if (in_array($normalizedContentType, $normalizedContentTypes))
                    return $contentType;
        }

        return $default;
    }

    public function getContentTypes()
    {
        return $this->contentTypes;
    }

    public function setContentTypes($contentTypes)
    {
        $this->contentTypes = $contentTypes;
    }

    public function isFormatSupportedForSerialization($format)
    {
        return in_array($format, array("json", "xml", "yaml"));
    }
}
