<?php

namespace Kbrw\RiakBundle\Model\Bucket;

interface BucketInterface
{
    /**
     * @param  array<string> | string          $keys
     * @return \Kbrw\RiakBundle\Model\KV\Datas
     */
    public function fetch($keys);

    /**
     * @param  string                         $keys
     * @return \Kbrw\RiakBundle\Model\KV\Data
     */
    public function uniq($key);

    /**
     * @param  array<string, mixed> $objects
     * @return boolean
     */
    public function put($objects);

    /**
     * @param  array<string> $keys
     * @return boolean
     */
    public function delete($keys);

    /**
     * @return array<string>
     */
    public function keys();

    /**
     * @return integer
     */
    public function count();

    public function search($query);
}