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

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\FileLocator;
use UCI\Boson\ExcepcionesBundle\Loader\XmlFileReader;

class XmlFileReaderTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixturesPath;

    protected function setUp()
    {
        if (!class_exists('Symfony\Component\Config\Loader\Loader')) {
            $this->markTestSkipped('The "Config" component is not available');
        }
    }

    public static function setUpBeforeClass()
    { $rutaArray = explode('/src/',__DIR__);
        $rutaArray = explode('/src/', __DIR__);
        if(count($rutaArray)==1){
            $rutaArray = explode('/vendor/', __DIR__);
        }
        self::$fixturesPath = realpath($rutaArray[0].'/vendor/symfony/symfony/src/Symfony/Component/DependencyInjection/Tests/Fixtures/');
        require_once self::$fixturesPath.'/includes/foo.php';
        require_once self::$fixturesPath.'/includes/ProjectExtension.php';
        require_once self::$fixturesPath.'/includes/ProjectWithXsdExtension.php';
    }

    public function testLoad()
    {
        $loader = new XmlFileReader(new FileLocator(__DIR__.'/../Fixtures/'));
        $array = $loader->load('foo.xml');
        $this->assertTrue(is_array($array));
        $this->assertTrue(count($array['example']) == 2);
        $this->assertEquals($array['example'][0], "Example 1");
        try {
            $array = $loader->load('foo2.xml');
            $this->fail('->load() throws an InvalidArgumentException if the loaded file does not exist');

        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e, '->load() throws an InvalidArgumentException if the loaded file does not exist');
            $this->assertStringStartsWith('The file "foo2.xml" does not exist (in:', $e->getMessage(), '->load() throws an InvalidArgumentException if the loaded file does not exist');
        }
    }

    public function testSupports()
    {
        $loader = new XmlFileReader(new FileLocator());

        $this->assertTrue($loader->supports('foo.xml'), '->supports() returns true if the resource is loadable');
        $this->assertFalse($loader->supports('foo.foo'), '->supports() returns true if the resource is loadable');
    }
}
