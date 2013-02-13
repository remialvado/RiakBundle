<?php

namespace Kbrw\RiakBundle\Session\Storage\Handler;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * RiakSessionHandler.
 *
 * Session handler using Remi Avlvado's RiakBundle to access Riak database.
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class RiakSessionHandler implements \SessionHandlerInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $clusterServiceId;

    /**
     * @var string
     */
    private $bucketName;

    /**
     * Constructor
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Define cluster service ID
     *
     * @param string $clusterServiceId
     */
    public function setClusterServiceId($clusterServiceId)
    {
        $this->clusterServiceId = $clusterServiceId;
    }

    /**
     * Define bucket name for storing sessions
     *
     * @param string $bucketName
     */
    public function setBucketName($bucketName)
    {
        $this->bucketName = $bucketName;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        $bucket = $this->container
                       ->get($this->clusterServiceId)
                       ->getBucket($this->bucketName);
        $bucket->setFormat('');

        $data    = $bucket->uniq($sessionId);
        $content = $data->getContent();

        return $content;
    }

    /**
     * {@inheritdoc}
     *
     * {@internal PHP session data is already natively serialized. }}
     *
     */
    public function write($sessionId, $data)
    {
        $bucket = $this->container
                       ->get($this->clusterServiceId)
                       ->getBucket($this->bucketName);
        $bucket->setFormat('');
        $bucket->put(array($sessionId => $data));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        $bucket = $this->container
                       ->get($this->clusterServiceId)
                       ->getBucket($this->bucketName);
        $bucket->delete($sessionId);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($lifetime)
    {
        return true;
    }
}
