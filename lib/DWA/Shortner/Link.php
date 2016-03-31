<?php

namespace DWA\Shortner;

final class Link
{
    /** @var string shortner path (ie:  '/foobar' from 'http://dwa.io/foobar') */
    private $path;

    /** @var string target domain */
    private $domain;

    /** @var string key to update link */
    private $key;

    /** @var int hit counter */
    private $hits;

    /**
     * @param string $path
     * @param string $domain
     * @param string $key
     * @param int $hits
     */
    function __construct($path, $domain, $key, $hits)
    {
        if (empty($path)) {
            throw new \InvalidArgumentException('Invalid path');
        }

        $parts = parse_url($domain);
        if (! isset($parts['scheme'])) {
            $parts['scheme'] = 'http';
            $domain = 'http://' . $domain;
        }
        if (! isset($parts['host']) && ! isset($parts['path'])) {
            throw new \InvalidArgumentException('Invalid domain URL');
        }
        if (strpos($parts['scheme'], 'http') !== 0 && strpos($parts['scheme'], 'https') !== 0) {
            throw new \InvalidArgumentException('Invalid domain URL');
        }
        if (!filter_var($domain, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid domain URL');
        }

        $this->path = $path;
        $this->domain = $domain;
        $this->key = $key;
        $this->hits = $hits;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return int
     */
    public function getHits()
    {
        return $this->hits;
    }

}
