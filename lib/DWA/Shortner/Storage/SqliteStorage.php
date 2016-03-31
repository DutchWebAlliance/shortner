<?php

namespace DWA\Shortner\Storage;

use DWA\Shortner\Link;

class SQLiteStorage implements LinkStorageInterface
{
    /** @var \SQLite3 */
    private $db;

    function __construct($filename = 'shortner.sq3')
    {
        $this->db = new \SQLite3($filename);
        if (!$this->db) {
            throw new \RuntimeException('Cannot open database');
        }

        $this->db->exec('CREATE TABLE IF NOT EXISTS links (path VARCHAR(50) PRIMARY KEY, _key VARCHAR(10), domain VARCHAR(100), hits INTEGER);');
    }


    /**
     * @return Link|null
     */
    public function findLink($path)
    {
        $stmt = $this->db->prepare('SELECT * FROM links WHERE path=:path');
        $stmt->bindValue('path', $path);

        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);

        if (!$row) {
            return null;
        }

        return new Link($row['link'], $row['domain'], $row['_key'], $row['hits']);
    }

    /**
     * @param Link $link
     * @return Link
     */
    public function saveLink(Link $link)
    {
        $stmt = $this->db->prepare('INSERT OR REPLACE INTO links (path, domain, _key, hits) VALUES (:path, :domain, :key, :hits)');
        $stmt->bindValue('path', $link->getPath());
        $stmt->bindValue('domain', $link->getDomain());
        $stmt->bindValue('key', $link->getKey());
        $stmt->bindValue('hits', $link->getHits());
        $stmt->execute();

        return $link;
    }

    /**
     * @param Link $link
     * @return mixed
     */
    public function increaseHitcount(Link $link)
    {
        $stmt = $this->db->prepare('UPDATE links SET hits=hits+1 WHERE path=:path');
        $stmt->bindValue('path', $link->getPath());
        $stmt->execute();

        // Reload from database, it has changed
        return $this->findLink($link->getPath());
    }

}
