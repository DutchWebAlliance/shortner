<?php

namespace DWA\Shortner;

use DWA\Shortner\Exception\IncorrectKeyException;
use DWA\Shortner\Storage\LinkStorageInterface;

class Shortner
{

    /**
     * @param string $filename
     */
    public function __construct(LinkStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Creates or updates a link.
     *
     * @param string $path
     * @param string $domain
     * @param string $key
     * @return Link
     */
    public function shorten($path, $domain, $key = "")
    {
        $link = $this->findLink($path);
        if (! $link) {
            $link = new Link($path, $domain, mt_rand(), 0);
        } else {
            $link = $this->updateLink($link, $domain, $key);
        }

        return $this->saveLink($link);
    }


    /**
     * Returns link entity based from path, or null when not found
     *
     * @param $path
     * @return Link|null
     */
    public function findLink($path)
    {
        return $this->storage->findLink($path);
    }

    /**
     * Returns link from path or throws exception
     *
     * @param $path
     * @return Link
     */
    public function getLink($path)
    {
        $link = $this->findLink($path);
        if (! $link) {
            throw new \InvalidArgumentException('No link found for path');
        }

        return $link;
    }

    /**
     * Increases the hitcount on a link and reloads it from database.
     *
     * @param Link $link
     * @return Link
     */
    public function increaseHitCount(Link $link)
    {
        return $this->storage->increaseHitcount($link);

    }

    /**
     * Saves link to database
     *
     * @param Link $link
     * @return Link
     */
    protected function saveLink(Link $link)
    {
        return $this->storage->saveLink($link);
    }

    /**
     * Create a new link but only if key matches
     *
     * @param Link $link
     * @param $domain
     * @param $key
     * @return Link
     */
    protected function updateLink(Link $link, $domain, $key)
    {
        // Check key to see if we are allowed to update
        if ($link->getKey() != $key) {
            throw new IncorrectKeyException('Incorrect credentials');
        }

        return new Link($link->getPath(), $domain, $link->getKey(), 0);
    }


}

