<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Tests\Loader;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use UCI\Boson\ExcepcionesBundle\Loader\YamlFileReader;

class YamlFileReaderTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixturesPath;


    public function testLoadFile()
    {
        $loader = new YamlFileReader(new FileLocator(__DIR__));
        $r = new \ReflectionObject($loader);
        $m = $r->getMethod('loadFile');
        $m->setAccessible(true);

        $respuesta = $m->invoke($loader, 'foo.yml');
        $this->assertNull($respuesta);
        try {
            $respuesta = $m->invoke($loader, 'http://google.com');

        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class ,$e);
        }
    }

    public function testLoadParameters()
    {
        $loader = new YamlFileReader( new FileLocator(__DIR__));
        $loaded = $loader->load('excepciones.yml');
        $this->assertTrue(is_array($loaded));
        $this->assertArrayHasKey('excepciones', $loaded);
    }

    public function testSupports()
    {
        $loader = new YamlFileReader(new FileLocator(__DIR__));

        $this->assertTrue($loader->supports('foo.yml'), '->supports() returns true if the resource is loadable');
        $this->assertFalse($loader->supports('foo.foo'), '->supports() returns true if the resource is loadable');
    }

    public function testNonArrayTagThrowsException()
    {
        $loader = new YamlFileReader( new FileLocator(__DIR__));
        try {
            $loaded = $loader->load('badtag1.yml');

            $this->assertNull($loaded);
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertStringStartsWith('The file "badtag1.yml" does not exist', $e->getMessage());
        }
    }

    public function testValidate()
    {

        $loader = new YamlFileReader(new FileLocator(__DIR__));
        $r = new \ReflectionObject($loader);
        $m = $r->getMethod('validate');
        $m->setAccessible(true);

        $respuesta = $m->invoke($loader, null,'excepciones.yml');
        $this->assertNull($respuesta);
    }
}
