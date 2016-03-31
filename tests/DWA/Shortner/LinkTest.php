<?php

namespace DWA\Tests;

use DWA\Shortner\Link;

class LinkTest extends \PHPUnit_Framework_TestCase
{

    public function validConstructorsProvider() {
        return array(
            array('foo', 'https://www.google.com', '1234', 0),
            array('foo', 'http://www.google.com', '1234', 0),
            array('foo', 'www.google.com', '1234', 0),
            array('foo', 'dwa.io', '1234', 0),
            array('foo', 'io', '1234', 0),
            array('foo', 'sub.sub.sub.sub.io', '1234', 0),
            array('a', 'https://www.google.com', '', 0),
            array('sdfasdfasfs', 'https://www.google.com', '', -1),
            array('fasdf/asfd/asdf/asdfa/sdfasoo', 'https://www.google.com', '', 52),
        );
    }

    /**
     * @dataProvider validConstructorsProvider
     */
    function testValidConstructors($path, $domain, $key, $hit) {
        new Link($path, $domain, $key, $hit);
    }


    public function invalidConstructorsProvider() {
        return array(
            array('', 'https://', '1234', 0, 'InvalidArgumentException', 'Invalid path'),

            array('foo', 'https://', '1234', 0, 'InvalidArgumentException', 'Invalid domain URL'),
            array('foo', 'http://', '1234', 0, 'InvalidArgumentException', 'Invalid domain URL'),
            array('foo', '', '1234', 0, 'InvalidArgumentException', 'Invalid domain URL'),
            array('fasdf/asfd/asdf/asdfa/sdfasoo', 'javascript:alert("foo");', '', 52, 'InvalidArgumentException', 'Invalid domain URL'),
            array('fasdf/asfd/asdf/asdfa/sdfasoo', 'mailto://info@example.org', '', 52, 'InvalidArgumentException', 'Invalid domain URL'),
            array('fasdf/asfd/asdf/asdfa/sdfasoo', 'skype://microsoft', '', 52, 'InvalidArgumentException', 'Invalid domain URL'),
        );
    }

    /**
     * @dataProvider invalidConstructorsProvider
     */
    function testInvalidConstructors($path, $domain, $key, $hit, $exception, $msg) {
        $this->setExpectedException($exception, $msg);

        new Link($path, $domain, $key, $hit);
    }

    function testGetters() {
        $link = new Link('foobar', 'http://example.org', 'key', 1234);

        $this->assertEquals('foobar', $link->getPath());
        $this->assertEquals('http://example.org', $link->getDomain());
        $this->assertEquals('key', $link->getKey());
        $this->assertEquals(1234, $link->getHits());

        $clone = clone $link;
        $this->assertEquals('foobar', $clone->getPath());
        $this->assertEquals('http://example.org', $clone->getDomain());
        $this->assertEquals('key', $clone->getKey());
        $this->assertEquals(1234, $clone->getHits());

    }

}

