<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 30/09/14
 * Time: 20:59
 */

namespace UCI\Boson\ExcepcionesBundle\Loader;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\DependencyInjection\SimpleXMLElement;

/**
 * Class XmlFileLoader. Se encarga de cargar la información definida en los ficheros de configuración en formato XML
 *
 * @author Daniel Arturo Casals Amat <dacasals@uci.cu>
 * @package UCI\Boson\ExcepcionesBundle\Loader
 */
class XmlFileLoader extends FileLoader {

    /**
     * Carga un recurso de tipo Xml
     *
     * @param mixed $file
     * @param null $type
     * @return array
     */
    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

         $dom = XmlUtils::loadFile($path);

        $arrayXml = XmlUtils::convertDomElementToArray($dom->documentElement);
        $this->container->addResource(new FileResource($path));
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