<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25/06/15
 * Time: 8:25
 */
namespace UCI\Boson\ExcepcionesBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use UCI\Boson\ExcepcionesBundle\Controller\ViewController;

class ViewControllerTest extends WebTestCase
{


    /**
     *  Obtiene un arreglo de excepciones dado un path del fichero de excepciones y el formato
     */
    public function testobtenerDA()
    {
        $controller = new ViewController();
        $result = $controller->obtenerDA(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'excepciones.yml', 'yml');
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('CE01', $result));
        $result = $controller->obtenerDA(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'excepciones.xml', 'xml');
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('E605', $result));
    }

    /**
     *  Obtiene un arreglo de excepciones dado un path del fichero de excepciones y el formato  con su traducción
     */
    public function testobtener()
    {
        $controller = new ViewController();
        $result = $controller->obtener(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'excepciones.yml', 'yml');
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('CE01', $result));

        $result = $controller->obtener(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'excepciones.xml', 'xml');
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('E605', $result));

    }

//
    public function testIndexAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/excepciones/getAll');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains(""ExcepcionesBundle":{")')->count()
        );
    }

    public function test()
    {
        $controller = new ViewController();
        $refClass = new \ReflectionClass($controller);

        $m = $refClass->getMethod('getTranslationByRoute');
        $m->setAccessible(true);

        $respuesta = $m->invokeArgs($controller, array(__DIR__.DIRECTORY_SEPARATOR.'translations'));
        $this->assertTrue(is_array($respuesta));
        try{
            $respuesta = $m->invokeArgs($controller, array(__DIR__.DIRECTORY_SEPARATOR.'translationsmal'));
        }
        catch(\Exception $e){
            $this->assertEquals($e->getMessage(),"Las configuraciones de traducción realizadas están mal");
        }

    }


}
 