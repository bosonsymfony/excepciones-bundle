<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/09/14
 * Time: 13:20
 */

namespace UCI\Boson\ExcepcionesBundle\Exception;

use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;
use UCI\Boson\ExcepcionesBundle\Loader\XmlFileReader;
use UCI\Boson\ExcepcionesBundle\Loader\YamlFileReader;
use Symfony\Component\Config\FileLocator;

/**
 * Class LocalException. Clase genérica para todas las excepciones locales de los bundles
 *
 * @author Daniel Arturo Casals Amat <dacasals@uci,cu>
 * @package UCI\Boson\ExcepcionesBundle\Exception
 */
class LocalException extends \Exception
{

    private $bundleName = '';
    private $clase = '';
    private $linea = '';
    private $interna = null;
    private $descripcion = '';
    private $metodo = '';
    private $mensaje = '';
    private $showInProd = false;
    private $codigo  = 0;
    private $trazas = array();

    /**
     * Constructor de la Clase LocalException
     * Responde al RF(57) (Configurar y registrar excepciones)
     *
     * @param string $codigo  Código de la excepción.
     * @param null $interna  Excepción previa generada por el sistema.
     * @param string $locale  Idioma  a mostrar la excepción, español por defecto.
     * @throws \Exception   excepción generada si no existe ninguna registrada con el código especificado.
     */

    function __construct($codigo, $interna = null,$locale = null)
    {

        $arrayRutaFile = explode(DIRECTORY_SEPARATOR, $this->file);
        $strExtraer = "";
        $direccionBundle = explode(DIRECTORY_SEPARATOR.$arrayRutaFile[count($arrayRutaFile) -1],$this->file);
        for ($i = count($arrayRutaFile) -1 ; $i >=0 ; $i--) {
            if (preg_match('/[a-zA-Z0-9]Bundle$/', $arrayRutaFile[$i]) == 1) {
                $direccionBundle = explode($strExtraer,$this->file)[0];
                $this->bundleName = $arrayRutaFile[$i];
                $direccionFileExcepciones = $direccionBundle  . DIRECTORY_SEPARATOR ."Resources".DIRECTORY_SEPARATOR."config";
                if(is_dir($direccionFileExcepciones))
                    break;
            }
            $strExtraer =DIRECTORY_SEPARATOR.$arrayRutaFile[$i].$strExtraer;
        }
        $value = $this->getArrayExcepcionesInFile($direccionFileExcepciones);

        if (!$this->existeExcepcionByCode($codigo, $value)) {
            throw new \Exception("Este bundle no tiene ninguna excepción definida con ese código, revice las excepciones definidas en :" . $direccionFileExcepciones . "/excepciones.(yml, xml)", 0, $interna);
        }
        $loader = new YamlFileLoader();
        if($locale == null){
            $parcer = new Yaml();
            $parametersYMl = $parcer->parse($this->getRootDir($direccionBundle).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'parameters.yml');
            $locale = $parametersYMl['parameters']['locale'];
        }

        $parametrosExcepcion = $value[$codigo];
        $this->mensaje = $parametrosExcepcion['mensaje'];
        $this->message = $this->mensaje;
        $this->descripcion = $parametrosExcepcion['descripcion'];
        $resource = $direccionBundle . DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'translations'.DIRECTORY_SEPARATOR.'translatesexcepciones.' . $locale . '.yml';
        if(is_readable($resource)){
            try{
                $catalogo = $loader->load($resource, $locale, 'translatesexcepciones');
                $parametrosExcepcion = $value[$codigo];
                if(array_key_exists('mensaje',$parametrosExcepcion)) {
                    $this->mensaje = $catalogo->get($parametrosExcepcion['mensaje'], 'translatesexcepciones');
                    $this->message = $this->mensaje;
                }
                if(array_key_exists('mensaje',$parametrosExcepcion))
                {
                    $this->descripcion = $catalogo->get($parametrosExcepcion['descripcion'], 'translatesexcepciones');
                }
            }
            catch(NotFoundResourceException $ex){
            }
        }

        if(array_key_exists('show_in_prod',$parametrosExcepcion))
        {
            $this->showInProd = $parametrosExcepcion['show_in_prod'];
        }
        $this->codigo = $codigo;
        $this->code = $this->codigo;
        $this->clase = $this->file;
        $this->linea = $this->line;
        $this->trazas = $this->getTrace();
        $this->metodo = $this->trazas[0]['function'];
        $this->interna = $interna;


    }

    /**
     * Retorna true o false si encuentra o no el la excepción con el código especificado.
     *
     * @param $codigo código especificado en el constructor de la clase
     * @param $arrayExcepciones  array de excepciones registradas en el archivo excepciones.(yml,xml,php) del bundle
     * @return boolean true si encuentra la excepción con el código especificado.
     */
    private function existeExcepcionByCode($codigo, $arrayExcepciones)
    {
        if(count($arrayExcepciones) != 0){

            foreach ($arrayExcepciones as $key => $value) {
                if ($key == $codigo)
                    return true;
            }
            return false;
        }
    }

    /**
     * Retorna la linea desde la que se generó la excepciòn.
     *
     * @return string
     */
    public function getLinea()
    {
        return $this->linea;
    }


    /**
     * Retorna el método desde el cual se generó la excepción.
     * Responde al RF(65) Obtener la acción de la clase que disparó la excepción
     *
     * @return string
     */
    public function getMetodo()
    {
        return $this->metodo;
    }


    /**
     * retorna un arreglo de excepciones cadena  denominadas trazas.
     *
     * @return array
     */
    public function getTrazas()
    {
        return $this->trazas;
    }


    /**
     * Retorna el nombre del bundle desde el cual es lanzada la excepción.
     * Responde al RF (66) Obtener el bundle en el que ocurrió la excepción
     *
     * @return string
     */
    public function getBundleName()
    {
        return $this->bundleName;
    }


    /**
     * Retorna la ruta de la clase desde la que se lanzó la excepción.
     * Responde al RF (64) (Obtener la clase que lanzó la excepción)
     *
     * @return string
     */
    public function getClase()
    {
        return $this->clase;
    }


    /**
     * Retorna el código de la excepción lanzada.
     * Responde al RF (60) (Obtener el identificador o código de la excepción)
     * @return int|string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }


    /**
     * Retorna la descripción de la excepción.
     * Responde al RF (62) (Obtener la descripción de la excepción)

     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }


    /**
     * Retorna la excepción interna, pasada en el constructor, null en caso de no especificarse.
     * Responde al RF (61) (Obtener la excepción interna que ocurrió)

     * @return \Exception
     */
    public function getInterna()
    {
        return $this->interna;
    }


    /**
     * Obtiene el mensaje de la excepción
     * Responde al RF(63) Obtener el tipo de la excepción
     *
     * @return string
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }

    /**
     * Obtiene el arreglo de excepciones configuradas in el fichero primero yml  y si no está  xml
     *
     * @param $direccionFile
     * @return array
     * @throws \InvalidArgumentException En caso de no encontrar el fichero o no estar bien estructurado.
     */
    public function getArrayExcepcionesInFile($direccionFile)
    {
        $locator = new FileLocator($direccionFile);
        try {
            $loader = new YamlFileReader($locator);

            $locator->locate("excepciones.yml");
            $excepciones = $loader->load('excepciones.yml');
            return $excepciones['excepciones'];

        } catch (\InvalidArgumentException $exc) {
            try {
                $loader = new XmlFileReader($locator);
                $locator->locate("excepciones.xml");
                $excepciones = $loader->load('excepciones.xml');
                return $excepciones;
            } catch (\InvalidArgumentException $exc) {
                throw $exc;
            }
        }
    }

    /**
     * @param $dir
     * @return mixed
     */
    private function getRootDir($dir){
        $dirArray = explode(DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR,$dir);
        if(count($dirArray) ==1){
            $dirArray = explode(DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR,$dir);

        }
        return $dirArray[0];
    }

    /**
     * @return string
     */
    public function getShowInProd()
    {
        return $this->showInProd;
    }

}