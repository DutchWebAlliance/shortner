<?php

namespace DWA\Shortner\Storage;

use DWA\Shortner\Link;

interface LinkStorageInterface
{
    /**
     * @param string $path
     * @return Link|null
     */
    public function findLink($path);

    /**
     * @param Link $link
     * @return Link
     */
    public function saveLink(Link $link);

    /**
     * @param Link $link
     * @return Link
     */
    public function increaseHitcount(Link $link);

}

