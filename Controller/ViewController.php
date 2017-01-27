<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/05/15
 * Time: 8:41
 */
namespace UCI\Boson\ExcepcionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;


class ViewController extends Controller
{

    /**
     * @return Response
     */
    public function indexAction()
    {
        $excepciones = array();
        $bundldes = $this->container->get('kernel')->getBundles();
        foreach ($bundldes as $b) {
            if (file_exists($dir = $b->getPath() . '/Resources/config/excepciones.yml')) {
                $excepciones[$b->getName()] = $this->obtener($dir, 'yml');
            } else if (file_exists($dir = $b->getPath() . '/Resources/config/excepciones.xml')) {
                $excepciones[$b->getName()] = $this->obtener($dir, 'xml');
            }
        }
        return new Response(json_encode($excepciones, JSON_UNESCAPED_UNICODE), 200);
    }

    /* Este método debe implementarse una vez creada la interfaz visual
    public function cambiarExcepcionesAction(Request $request)
    {
        $this->cambiar($request);
    }
    */


    /**
     * Obtiene un arreglo de excepciones dado un path del fichero de excepciones y el formato.
     *
     * @param $file
     * @param $extension
     * @return mixed
     */
    public function obtenerDA($file, $extension)
    {
        try{
            if ($extension == 'yml') {
                $values = Yaml::parse($file);
                return $values['excepciones'];
            } else if ($extension == 'xml') {
                $xml = XmlUtils::loadFile($file);
                $values = XmlUtils::convertDomElementToArray($xml->documentElement);
                return $values;
            }
        }
        catch(\Exception $e){
            return false;
        }
    }

    /**
     * Obtiene un arreglo de excepciones dado un path del fichero de excepciones y el formato  con su traducción.
     *
     * @param $file
     * @param $extension
     * @return mixed
     */
    public function obtener($file, $extension)
    {
        if ($extension == 'yml') {
            $trasDir = str_replace("config" . DIRECTORY_SEPARATOR . "excepciones.yml", "translations", $file);
            $traducciones = $this->getTranslationByRoute($trasDir);
            $values = Yaml::parse($file);
            $respuesta = $this->getExceptionsWithTrans($traducciones, $values['excepciones']);
            return $respuesta;
        } else if ($extension == 'xml') {
            $trasDir = str_replace("config" . DIRECTORY_SEPARATOR . "excepciones.xml", "translations", $file);
            $traducciones = $this->getTranslationByRoute($trasDir);
            $xml = XmlUtils::loadFile($file);
            $values = XmlUtils::convertDomElementToArray($xml->documentElement);
            $respuesta = $this->getExceptionsWithTrans($traducciones, $values);
            return $respuesta;
        }
        return false;
    }

    /**
     *
     *
     * @param $traducciones
     * @param $values
     * @return mixed
     */
    private function getExceptionsWithTrans($traducciones, $values)
    {
        foreach ($values as $key => $excep) {
            if ($excep['mensaje'] === "excepciones." . $key . ".mensaje") {
                foreach ($traducciones as $keyId => $arrayIdiomaTrans) {
                    $langExcepCurrent = array();
                    if (array_key_exists($key, $arrayIdiomaTrans)) {
                        $langExcepCurrentM['mensaje'] = $arrayIdiomaTrans[$key]['mensaje'];
                        $values[$key]['translations'][$keyId] = $langExcepCurrentM;
                    }
                }
            }
            if ($excep['descripcion'] === "excepciones." . $key . ".descripcion") {
                foreach ($traducciones as $keyId => $arrayIdiomaTrans) {
                    $langExcepCurrent = array();
                    if (array_key_exists($key, $arrayIdiomaTrans)) {
                        $langExcepCurrentM['descripcion'] = $arrayIdiomaTrans[$key]['descripcion'];
                        $values[$key]['translations'][$keyId] = $langExcepCurrentM;
                    }
                }
            }
        }
        return $values;
    }

    /**
     * @param $route
     * @return array|Response
     */
    private function getTranslationByRoute($route)
    {
        $arrayFileTrans = scandir($route);
        $traducciones = array();
        foreach ($arrayFileTrans as $fichero) {
            if (strpos($fichero, 'translatesexcepciones') !== false) {
                $idioma = str_replace("translatesexcepciones.", "", $fichero);
                $idioma = str_replace(".yml", "", $idioma);
                $ExcepTraduccidas = Yaml::parse($route . DIRECTORY_SEPARATOR . $fichero);
                if(array_key_exists('excepciones',$ExcepTraduccidas)){
                    $traducciones[$idioma] = $ExcepTraduccidas['excepciones'];
                }
                else {
                    return new Response("Las configuraciones de traducción realizadas están mal",500);
                }
            }
        }
        return $traducciones;
    }
    /*
    public function cambiar(Request $request)
    {
        // $values = $this->obtener();
        $values['parameters']['cache'] = $request->request->all();
        $result = Yaml::dump($values, 3);

        file_put_contents($this->getRootDir(), $result);

        var_dump($values);

    }

    /**
     * Obtiene la ruta directa al fichero de configuración de boson.
     *
     * @return string
     */
   /* public function getRootDir()
    {
        return $this->container->get('kernel')->getRootDir() . '/config/parameters_boson.yml';
    }*/
}