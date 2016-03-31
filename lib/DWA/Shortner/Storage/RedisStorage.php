<?php

namespace DWA\Shortner\Storage;

use DWA\Shortner\Link;
use Predis\Client;

class RedisStorage implements LinkStorageInterface
{
    /** @var Client */
    private $redis;

    private $prefix;

    /**
     * @param array $params
     * @param string $prefix
     */
    function __construct($params = array(), $prefix = "dwaio_")
    {
        $this->redis = new Client($params);
        $this->prefix = $prefix;
    }


    /**
     * @return Link|null
     */
    public function findLink($path)
    {
        $row = $this->redis->hgetall($this->prefix . $path);
        if (!$row) {
            return null;
        }

        return new Link($row['path'], $row['domain'], $row['key'], $row['hits']);
    }

    /**
     * @param Link $link
     * @return Link
     */
    public function saveLink(Link $link)
    {
        $this->redis->hmset($this->prefix . $link->getPath(), array(
            'path' => $link->getPath(),
            'domain' => $link->getDomain(),
            'key' => $link->getKey(),
            'hits' => $link->gethits(),
        ));

        return $link;
    }

    /**
     * @param Link $link
     * @return mixed
     */
    public function increaseHitcount(Link $link)
    {
        $this->redis->hincrby($this->prefix . $link->getPath(), 'hits', 1);

        // Reload from database, it has changed
        return $this->findLink($link->getPath());
    }

}
