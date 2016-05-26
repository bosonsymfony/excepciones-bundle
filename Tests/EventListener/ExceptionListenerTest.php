<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UCI\Boson\ExcepcionesBundle\Tests\EventListener;

use  \Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use UCI\Boson\ExcepcionesBundle\EventListener\ExceptionListener;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
if(file_exists( __DIR__ . '/../../../../../../app/AppKernel.php')){
require_once __DIR__ . '/../../../../../../app/AppKernel.php';
}
else {
    require_once __DIR__ . '/../../../../../../../../app/AppKernel.php';
}
/**
 * ExceptionListenerTest
 *
 * @author Daniel Arturo Casals Amat <dacasas@uci.cu>
 */
class ExceptionListenerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        ///static::$container = self::$kernel->getContainer();
    }

    public function provider()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\Request')) {
            return array(array(null, null));
        }
        $request = new Request();

        $exception = new \Exception('foo');
        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');

        $event2 = new GetResponseForExceptionEvent($kernel, $request, 'foo', $exception);

        return array(
            array($event2)
        );
    }

    /**
     * @dataProvider provider
     */
    public function testOnKernelException(GetResponseForExceptionEvent $event){

      $listener = new ExceptionListener(static::$kernel->getContainer(),static::$kernel->getLogDir(),DIRECTORY_SEPARATOR.'foo.log');
        $listener->onKernelException($event);
        $resource = fopen(static::$kernel->getLogDir().DIRECTORY_SEPARATOR.'foo.log','r');
        $fileInfo = fread($resource,1000);
        fclose($resource);
        $this->assertRegExp('/mensaje: foo/', $fileInfo);
        $this->assertRegExp('/clase:/', $fileInfo);
        $this->assertRegExp('/ExceptionListenerTest.php/', $fileInfo);
        $this->assertRegExp('/linea:/', $fileInfo);

    }
}

