<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UCI\Boson\ExcepcionesBundle\Tests\Loader;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Loader\Loader;
use UCI\Boson\ExcepcionesBundle\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class XmlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixturesPath;

    protected function setUp()
    {
        if (!class_exists('Symfony\Component\Config\Loader\Loader')) {
            $this->markTestSkipped('The "Config" component is not available');
        }
    }

    public function testload2()
    {
        $xmlloader = new XmlFileLoader(new ContainerBuilder(), new FileLocator(__DIR__));
        $loaded = $xmlloader->load('excepciones.xml');
        $this->assertTrue(is_array($loaded));
        $this->assertArrayHasKey('E605', $loaded);
    }

    public static function setUpBeforeClass()
    {

        $rutaArray = explode('/src/', __DIR__);
        if(count($rutaArray)==1){
            $rutaArray = explode('/vendor/', __DIR__);
        }
        self::$fixturesPath = realpath($rutaArray[0] . '/vendor/symfony/symfony/src/Symfony/Component/DependencyInjection/Tests/Fixtures/');
        require_once self::$fixturesPath . '/includes/foo.php';
        require_once self::$fixturesPath . '/includes/ProjectExtension.php';
        require_once self::$fixturesPath . '/includes/ProjectWithXsdExtension.php';
    }

    public function testLoad()
    {
        $loader = new XmlFileLoader(new ContainerBuilder(), new FileLocator(self::$fixturesPath . '/ini'));

        try {
            $loader->load('fooo.xml');
        } catch (\Exception $e) {

            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertStringStartsWith('The file "fooo.xml" does not exist', $e->getMessage());
        }

    }

    public function testSupports()
    {
        $loader = new XmlFileLoader(new ContainerBuilder(), new FileLocator());

        $this->assertTrue($loader->supports('foo.xml'), '->supports() returns true if the resource is loadable');
        $this->assertFalse($loader->supports('foo.foo'), '->supports() returns true if the resource is loadable');
    }
}
