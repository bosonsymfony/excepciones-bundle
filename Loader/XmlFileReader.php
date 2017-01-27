<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 30/09/14
 * Time: 20:59
 */

namespace UCI\Boson\ExcepcionesBundle\Loader;

use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\Config\Loader\FileLoader;

/**
 * Class XmlFileLoader. Se encarga de cargar la información definida en los ficheros de configuración en formato XML
 *
 * @author Daniel Arturo Casals Amat <dacasals@uci.cu>
 * @package UCI\Boson\ExcepcionesBundle\Loader
 */
class XmlFileReader extends FileLoader {

    /**
     * Lee el contenido de un fichero XML.
     *
     * @param string $type The resource type
     * @return array.
     */
    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);
        try {
            $dom = XmlUtils::loadFile($path/*, array($this, 'validateSchema')*/);
        }
        catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(sprintf('Unable to parse file "%s".', $file), $e->getCode(), $e);
        }
        $arrayXml = XmlUtils::convertDomElementToArray(   $dom->documentElement);
        return $arrayXml;

    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string $type The resource type
     *
     * @return bool    true if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'xml' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}