<?php

namespace DWA\Tests;

use DWA\Shortner\Shortner;
use DWA\Shortner\Storage\MemoryStorage;

class ShortnerTest extends \PHPUnit_Framework_TestCase
{

    /** @var Shortner */
    protected $shortner;

    function setUp() {
        $this->shortner = new Shortner(new MemoryStorage());
    }

    function testFindLink()
    {
        $link = $this->shortner->shorten('foobar', 'http://www.example.org');
        $this->assertInstanceOf('DWA\Shortner\Link', $link);

        $this->assertEquals('foobar', $link->getPath());
        $this->assertEquals('http://www.example.org', $link->getDomain());
        $this->assertEquals(0, $link->getHits());
    }

    function testFindLinkNotFound()
    {
        $link = $this->shortner->findLink('foobar');
        $this->assertNull($link);
    }

    function testGetLinkFound()
    {
        $link = $this->shortner->shorten('foobar', 'http://www.example.org');
        $this->assertInstanceOf('DWA\Shortner\Link', $link);

        $this->assertNotNull($this->shortner->getLink('foobar'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    function testGetLinkNotFound()
    {
        $this->shortner->getLink('not-exists');
    }


    function testHitCount()
    {
        $link = $this->shortner->shorten('foobar', 'http://www.example.org');
        $this->assertInstanceOf('DWA\Shortner\Link', $link);

        $this->assertEquals(0, $link->getHits());

        $newLink = $this->shortner->increaseHitCount($link);
        $this->assertEquals(1, $newLink->getHits());

        $this->assertEquals(0, $link->getHits());
    }

    function testShorten()
    {
        $link = $this->shortner->shorten('foobar', 'http://www.example.org');
        $this->assertInstanceOf('DWA\Shortner\Link', $link);
        $key = $link->getKey();

        $link = $this->shortner->increaseHitCount($link);
        $link = $this->shortner->increaseHitCount($link);

        $link = $this->shortner->findLink('foobar');
        $this->assertEquals(2, $link->gethits());
        $this->assertEquals('http://www.example.org', $link->getDomain());

        $link = $this->shortner->shorten('foobar', 'http://www.google.com', $key);
        $this->assertInstanceOf('DWA\Shortner\Link', $link);
        $this->assertEquals('http://www.google.com', $link->getDomain());
        $this->assertEquals(0, $link->gethits());
        $this->assertEquals($key, $link->getKey());
    }

    /**
     * @expectedException DWA\Shortner\Exception\IncorrectKeyException
     */
    function testShortenWithIncorrectKey()
    {
        $this->shortner->shorten('foobar', 'http://www.example.org');
        $this->shortner->shorten('foobar', 'http://www.google.com', 'incorrect-key');
    }
}
