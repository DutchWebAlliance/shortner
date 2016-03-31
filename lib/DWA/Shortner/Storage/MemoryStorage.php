<?php

namespace DWA\Shortner\Storage;

use DWA\Shortner\Link;

class MemoryStorage implements LinkStorageInterface
{

    protected $links = array();

    /**
     * @return Link|null
     */
    public function findLink($path)
    {
        if (isset($this->links[$path])) {
            return $this->links[$path];
        }

        return null;
    }

    /**
     * @param Link $link
     * @return Link
     */
    public function saveLink(Link $link)
    {
        $this->links[$link->getPath()] = $link;
        return $link;
    }

    /**
     * @param Link $link
     * @return Link
     */
    public function increaseHitcount(Link $link)
    {
        return $this->saveLink(
            new Link($link->getPath(), $link->getDomain(), $link->getKey(), $link->getHits() + 1)
        );
    }

}
