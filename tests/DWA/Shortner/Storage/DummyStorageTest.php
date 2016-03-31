<?php

namespace DWA\Tests\Storage;

use DWA\Shortner\Link;
use DWA\Shortner\Storage\MemoryStorage;

class DummyStorageTest extends \PHPUnit_Framework_TestCase
{

    function testStorage() {
        $storage = new MemoryStorage();

        $link = $storage->findLink('foobar');
        $this->assertNull($link);

        $link = new Link('foobar', 'http://example.org', 'key', 0);
        $link = $storage->saveLink($link);

        $this->assertInstanceOf('DWA\Shortner\Link', $link);
        $this->assertEquals('foobar', $link->getPath());
        $this->assertEquals('http://example.org', $link->getDomain());
        $this->assertEquals(0, $link->getHits());

        $link = $storage->increaseHitcount($link);
        $this->assertEquals(1, $link->getHits());

        $link = $storage->increaseHitcount($link);
        $link = $storage->increaseHitcount($link);
        $this->assertEquals(3, $link->getHits());

        $link = $storage->saveLink($link);
        $this->assertInstanceOf('DWA\Shortner\Link', $link);
    }

}

