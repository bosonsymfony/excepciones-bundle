<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 24/06/15
 * Time: 18:05
 */
namespace UCI\Boson\ExcepcionesBundle\Tests\Exception;

use Symfony\Component\Yaml\Yaml;
use UCI\Boson\ExcepcionesBundle\Exception\LocalException;

class LocalExceptionTest extends \PHPUnit_Framework_TestCase {

    public function testConstruct()
    {
        $excepcion = new LocalException('E605',new \Exception("mensaje en blanco"),'es');
        $this->assertInstanceOf(LocalException::class,$excepcion);
    }

    public function testParameters()
    {
        try{
            throw new LocalException('E605',new \Exception('El mensaje'));
        }catch (LocalException $ex){
            $this->assertTrue(is_integer($ex->getLinea()));
            $this->assertEquals('ExcepcionesBundle',$ex->getBundleName());

            $this->assertEquals(dirname(__FILE__).DIRECTORY_SEPARATOR.'LocalExceptionTest.php' , $ex->getClase());
            $this->assertEquals('E605' , $ex->getCodigo());
            $this->assertInstanceOf('\Exception' , $ex->getInterna());
            $this->assertTrue(is_string($ex->getDescripcion()));
            $this->assertTrue(is_integer(strpos($ex->getMensaje(),'translator')));
            $this->assertEquals('testParameters' , $ex->getMetodo());
            $this->assertTrue(is_array($ex->getTrazas())) ;
            $this->assertTrue(is_array( $ex->getArrayExcepcionesInFile(dirname(__FILE__))));
            $this->assertFalse($ex->getShowInProd());

            $reflection = new \ReflectionClass(get_class($ex));
            $method = $reflection->getMethod('existeExcepcionByCode');
            $method->setAccessible(true);
            $respuesta =  $method->invokeArgs($ex, array('Excep',$ex->getArrayExcepcionesInFile(dirname(__FILE__))));
            $this->assertTrue($respuesta) ;
            $respuestaFalse =  $method->invokeArgs($ex, array('codigonovalido',$ex->getArrayExcepcionesInFile(dirname(__FILE__))));
            $this->assertFalse($respuestaFalse) ;

            try{
                $this->assertInstanceOf('\InvalidArgumentException',$ex->getArrayExcepcionesInFile('dirIncorrecta'));
            }
            catch(\Exception $exInv){
                $this->assertInstanceOf('\InvalidArgumentException' , $exInv);
            }

        }

    }


}
 