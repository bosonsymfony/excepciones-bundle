<?php

namespace UCI\Boson\ExcepcionesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UCI\Boson\BackendBundle\Controller\BackendController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use UCI\Boson\ExcepcionesBundle\Entity\Data;
use UCI\Boson\ExcepcionesBundle\Form\DataType;


class ConfigController extends BackendController
{

    /**
     * Obtiene la dirección del fichero general de excepciones dado el nombre de un bundle
     * @param string $nbund
     * @return string
     */
    public function getDirGeneralByBundle($nbund)
    {
        $bundles = $this->container->get('kernel')->getBundles();

        foreach ($bundles as $b) {
            if ($b->getName() == $nbund) {
                if (file_exists($b->getPath() . '/Resources/config/excepciones.yml')) {
                    return $b->getPath() . '/Resources/config/excepciones.yml';
                } elseif (file_exists($b->getPath() . '/Resources/config/excepciones.xml')) {
                    return $b->getPath() . '/Resources/config/excepciones.xml';
                } else {
                    //si no existe el fichero general de excepciones se crea uno nuevo con yml
                    $dumperyml = new Dumper();
                    $yaml_dump = $dumperyml->dump(null);
                    $dirGeneral = $b->getPath() . '/Resources/config/excepciones.yml';
                    file_put_contents($dirGeneral, $yaml_dump);
                    return $b->getPath() . '/Resources/config/excepciones.yml';
                }
            }
        }
    }

    /**
     * Devuelve un listado con todos los bundles registrados en la aplicación
     * @return JsonResponse
     * @Route(path="/excepciones/getInfo", name="excepciones_get_info", options={"expose"=true})
     */
    public function getInfo()
    {
        $coleccion = array();
        $bundles = $this->container->get('kernel')->getBundles();

        foreach ($bundles as $b) {
            $datos = array(
                'NombreBundle' => $b->getName(),
            );
            array_push($coleccion, $datos);
        }
        return new JsonResponse($coleccion);
    }

    /**
     * Obtiene un listado de los bundles que tienen excepciones definidas en su fichero excepciones.yml o excepciones.xml
     * @return JsonResponse
     * @Route(path="/excepciones/getBundlesWithExceptions", name="excepciones_get_bundleswithexceptions", options={"expose"=true})
     */
    public function getBundlesWithExceptions()
    {
        $coleccion = array();
        $bundles = $this->container->get('kernel')->getBundles();

        foreach ($bundles as $b) {
            if (file_exists($b->getPath() . '/Resources/config/excepciones.yml') || file_exists($b->getPath() . '/Resources/config/excepciones.xml')) {
                //ademas de existir el archivo, su contenido debe ser != de null para que se liste como valido
                $extGen = $this->getExtensionGeneralByBundle($b->getName());
                if ($extGen == 'yml') {
                    $contenido = $this->getExceptionByDirYAML($b->getPath() . '/Resources/config/excepciones.yml');
                    if (!empty($contenido['excepciones'])) {
                        $datos = array(
                            'NombreBundle' => $b->getName(),
                        );
                        array_push($coleccion, $datos);
                    }
                } elseif ($extGen == 'xml') {
                    $contenido = $this->getExceptionByDirXML($b->getPath() . '/Resources/config/excepciones.xml');
                    if (!empty($contenido)) {
                        $datos = array(
                            'NombreBundle' => $b->getName(),
                        );
                        array_push($coleccion, $datos);
                    }
                }
            }
        }
        return new JsonResponse($coleccion);
    }

    /**
     * Devuelve el listado de excepciones dado el nombre de un bundle
     * Responde a los RF(82-83)Buscar o Listar excepciones por bundle
     * @param string $bundle
     * @return JsonResponse
     * @Route(path="/excepciones/getExceptionsbyBundle/{bundle}", name="excepciones_get_exceptionbybundle", options={"expose"=true})
     */
    public function getExceptionsByBundle($bundle)
    {
        $dir = $this->getDirGeneralByBundle($bundle);
        $extGen = $this->getExtensionGeneralByBundle($bundle);

        $response = array();

        if ($extGen == 'yml') {
            $excep = $this->getExceptionByDirYAML($dir);
            $excep_sn_front = $excep['excepciones'];
            if ($excep_sn_front != null) {
                foreach ($excep_sn_front as $key => $vf) {
                    array_push($response, $key);
                }
            }
        } elseif ($extGen == 'xml') {
            $excep = $this->getExceptionByDirXML($dir);
            //$excep_sn_front = $excep['excepciones'];
            if (!empty($excep)) {
                foreach ($excep as $key => $vf) {
                    array_push($response, $key);
                }
            }
        }
        return new JsonResponse($response);
    }

    /**
     * Devuelve las excepciones contenidas dentro de un fichero con extensión yml dado una dirección
     * @param mixed $dir
     * @return mixed
     */
    public function getExceptionByDirYAML($dir = null)
    {
        $yaml = new Parser();
        $values = $yaml->parse(file_get_contents($dir));
        return $values;
    }

    /**
     * Devuelve las excepciones contenidas dentro de un fichero con extensión xml dado una dirección
     * @param mixed $dir
     * @return mixed
     */
    public function getExceptionByDirXML($dir = null)
    {
        $xml = XmlUtils::loadFile($dir);;
        $values = XmlUtils::convertDomElementToArray($xml->documentElement);
        return $values;
    }

    /**
     * Devuelve la extensión del fichero de excepciones dado el nombre de un bundle
     * @param string $nbund
     * @return mixed
     */
    public function getExtensionGeneralByBundle($nbund = null)
    {
        $bund = array();
        $bundles = $this->container->get('kernel')->getBundles();

        foreach ($bundles as $b) {
            if ($b->getName() == $nbund) {
                if (file_exists($b->getPath() . '/Resources/config/excepciones.yml')) {
                    return 'yml';
                } elseif (file_exists($b->getPath() . '/Resources/config/excepciones.xml')) {
                    return 'xml';
                }
            }

        }
    }

    /**
     * Devuelve la dirección de la carpeta de traducciones dado el nombre de un bundle
     * @param string $nbund
     * @return string
     */
    public function getDirCarpetaTraslationByBundle($nbund = null)
    {
        $bundles = $this->container->get('kernel')->getBundles();

        foreach ($bundles as $b) {
            if ($b->getName() == $nbund) {
                return $b->getPath() . '/Resources/translations/';
            }
        }
    }

    /**
     * Devuelve el idioma de un fichero de traducciones dado el nombre del mismo
     * @param string $fichero
     * @return string
     */
    public function getIdiomaByFichero($fichero = null)
    {
        $idioma = str_replace("translatesexcepciones.", "", $fichero);
        $idioma = str_replace(".yml", "", $idioma);
        return $idioma;
    }

    /**
     *
     * Comprueba si la excepcion general tiene el parametro show_in_prod
     * @param string $bundle
     * @param string $codigoExcepcion
     * @return string
     * @Route(path="/excepciones/isShowInProd/{bundle}/{codigoExcepcion}", name="exception_isShowInProd", options={"expose"=true})
     */
    public function isShowInProd($bundle = null, $codigoExcepcion = null)
    {
        $dirGen = $this->getDirGeneralByBundle($bundle);
        $extGen = $this->getExtensionGeneralByBundle($bundle);
        if ($extGen == "yml") {
            $contenido = $this->getExceptionByDirYAML($dirGen);
            if (array_key_exists('show_in_prod', $contenido['excepciones'][$codigoExcepcion])) {
                if ($contenido['excepciones'][$codigoExcepcion]['show_in_prod'] == true) {
                    return new Response("true");
                } else {
                    return new Response("false");
                }
            } else {
                return new Response("false");
            }
        } elseif ($extGen == "xml") {
            $contenido = $this->getExceptionByDirXML($dirGen);
            if (array_key_exists('show_in_prod', $contenido[$codigoExcepcion])) {
                if ($contenido[$codigoExcepcion]['show_in_prod'] == true) {
                    return new Response("true");
                } else {
                    return new Response("false");
                }
            } else {
                return new Response("false");
            }
        }
    }

    /**
     * Devuelve un array con las traducciones de una excepcion dado el nombre del bundle y el código de la excepción
     * @Route(path="/excepciones/getTraslationsBundleCode/{bundle}/{codigoExcepcion}", name="get_exceptionTranslation_bundleCode", options={"expose"=true})
     * @param string $bundle
     * @param string $codigoExcepcion
     * @return JsonResponse
     */
    public function getTraslationsByBundleAndCode($bundle = null, $codigoExcepcion = null)
    {
        $translations = array();
        $dirTranslations = $this->getDirCarpetaTraslationByBundle($bundle);
        $arrayFileTrans = scandir($dirTranslations);

        foreach ($arrayFileTrans as $fichero) {
            if (strpos($fichero, 'translatesexcepciones') !== false) {
                $excep = $this->getExceptionByDirYAML($dirTranslations . $fichero);
                if (!empty($excep['excepciones'][$codigoExcepcion])) {
                    $idioma = $this->getIdiomaByFichero($fichero);
                    $translation = array(
                        'idioma' => $idioma,
                        'mensaje' => $excep['excepciones'][$codigoExcepcion]['mensaje'],
                        'descrip' => $excep['excepciones'][$codigoExcepcion]['descripcion']
                    );
                    array_push($translations, $translation);
                }
            }
        }
        return new JsonResponse($translations);
    }

    /**
     * Devuelve la dirección del fichero de traducción dado el nombre del bundle y el idioma
     * @param string $nbund
     * @param string $idioma
     * @return string
     */
    public function getDirTraslationByBundleIdioma($nbund = null, $idioma = null)
    {
        $bundles = $this->container->get('kernel')->getBundles();

        foreach ($bundles as $b) {
            if ($b->getName() == $nbund) {
                if (file_exists($b->getPath() . '/Resources/translations/translatesexcepciones.' . $idioma . '.yml')) {
                    return $b->getPath() . '/Resources/translations/translatesexcepciones.' . $idioma . '.yml';
                } elseif (file_exists($b->getPath() . '/Resources/translations/translatesexcepciones.' . $idioma . '.xml')) {
                    return $b->getPath() . '/Resources/translations/translatesexcepciones.' . $idioma . '.xml';
                } else {
                    //cuando el fichero translate no existe y hay q crear uno nuevo antes de almacenar los datos
                    $dumperyml = new Dumper();
                    $yaml_dump = $dumperyml->dump(null);
                    $dirTraslation = $b->getPath() . '/Resources/translations/translatesexcepciones.' . $idioma . '.yml';

                    //comprueba si no existe la carpeta, si no esta la creo directorio y luego el fichero
                    $dirCarpeta = $this->getDirCarpetaTraslationByBundle($b->getName());
                    if (!file_exists($dirCarpeta)) {
                        mkdir($b->getPath() . '/Resources/translations/');
                    }
                    file_put_contents($dirTraslation, $yaml_dump);
                    return $b->getPath() . '/Resources/translations/translatesexcepciones.' . $idioma . '.yml';
                }
            }
        }
    }

    /**
     * Comprueba dado la dirección de un fichero, su extensión y el código de la misma, si esta ya está definida
     * @param string $dirGen
     * @param string $extGeneral
     * @param string $codigoExcepcion
     * @return bool
     */
    public function existException($dirGen = null, $extGeneral = null, $codigoExcepcion = null)
    {
        if ($extGeneral == 'yml') {
            $contenido = $this->getExceptionByDirYAML($dirGen);
            if (!empty($contenido['excepciones'][$codigoExcepcion])) {
                // si no esta vacio, quiere decir que ya hay una excepcion en uso con ese codigo
                return true;
            } else {
                return false;
            }
        } elseif ($extGeneral == 'xml') {
            $contenido = $this->getExceptionByDirXML($dirGen);
            if (!empty($contenido[$codigoExcepcion])) {
                // si no esta vacio, quiere decir que ya hay una excepcion en uso con ese codigo
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Inserta una nueva excepción con sus traducciones
     * Responde al RF(79)Adicionar excepción por componente
     * @Route(path="/excepciones/insertData", name="exception_insert_data", options={"expose"=true})
     * @Method("POST")
     * @param Request $request
     * @return Response
     */
    public function InsertException(Request $request)
    {
        $dumperyml = new Dumper();

        $data = new Data();
        $formData = $this->createForm(new DataType(), $data);
        $formData->handleRequest($request);

        $listTranslation = $data->getListTranslation();

        $dir = $this->getDirGeneralByBundle($data->getBundle());

        //diferenciando extension de archivo
        $extensionGeneral = $this->getExtensionGeneralByBundle($data->getBundle());

        if ($this->existException($dir, $extensionGeneral, $data->getCodigo())) {
            return new Response("El código especificado está en uso por otra excepción", 500);
        }

        if ($extensionGeneral == "yml") {
            $yaml = $this->getExceptionByDirYAML($dir);
            if ($formData->isValid()) {
                $yaml['excepciones'][$data->getCodigo()]['mensaje'] = 'excepciones.' . $data->getCodigo() . '.mensaje';
                $yaml['excepciones'][$data->getCodigo()]['descripcion'] = 'excepciones.' . $data->getCodigo() . '.descripcion';
                if ($data->getShowprod() == "true") {
                    $yaml['excepciones'][$data->getCodigo()]['show_in_prod'] = true;
                }
                $yaml_dump = $dumperyml->dump($yaml, 6);
                file_put_contents($dir, $yaml_dump);
            }
        } elseif ($extensionGeneral == "xml") {
            $xml = $this->getExceptionByDirXML($dir);
            if ($formData->isValid()) {
                //adiciono los elementos nuevos
                $xml[$data->getCodigo()]['mensaje'] = 'excepciones.' . $data->getCodigo() . '.mensaje';
                $xml[$data->getCodigo()]['descripcion'] = 'excepciones.' . $data->getCodigo() . '.descripcion';
                if ($data->getShowprod() == "true") {
                    $xml[$data->getCodigo()]['show_in_prod'] = true;
                }

                //re-escribiendo en el xml:
                $rootNode = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><excepciones></excepciones>");

                foreach ($xml as $key => $value) {
                    $itemNode = $rootNode->addChild($key);
                    $itemNode->addChild('mensaje', $value['mensaje']);
                    $itemNode->addChild('descripcion', $value['descripcion']);
                    if (array_key_exists('show_in_prod', $value)) {
                        if ($value['show_in_prod'] == "true") {
                            $itemNode->addChild('show_in_prod', "true");
                        }
                    }
                }
                $rootNode->asXML($dir);
            }
        }
        //ahora con los translations q son todos en yml por suerte (porq lo son no ?!)
        foreach ($listTranslation as $t) {
            $dirTraslation = $this->getDirTraslationByBundleIdioma($data->getBundle(), $t['idioma']);
            $yaml = $this->getExceptionByDirYAML($dirTraslation); //porq solo es yml
            $yaml['excepciones'][$data->getCodigo()]['mensaje'] = $t['mensaje'];
            $yaml['excepciones'][$data->getCodigo()]['descripcion'] = $t['descrip'];
            $yaml_dump = $dumperyml->dump($yaml, 6);
            file_put_contents($dirTraslation, $yaml_dump);
        }

        return new Response();
    }

    /**
     * Elimina dado un bundle y un código las excepciones correspondientes
     * Responde al RF(81) Eliminar excepción por componente
     * @Route(path="/excepciones/eraseException/{bundle}/{codigoExcepcion}", name="exception_erase_data", options={"expose"=true})
     * @param string $bundle
     * @param string $codigoExcepcion
     * @return Response
     */
    public function BorrarExcepcion($bundle, $codigoExcepcion)
    {
        $dumper = new Dumper();

        //primero borrar el archivo general
        $dirGeneral = $this->getDirGeneralByBundle($bundle);
        $extGeneral = $this->getExtensionGeneralByBundle($bundle);

        if ($extGeneral == 'yml') {
            $yaml = $this->getExceptionByDirYAML($dirGeneral);
            unset($yaml['excepciones'][$codigoExcepcion]);
            $yaml_dump = $dumper->dump($yaml, 6);
            file_put_contents($dirGeneral, $yaml_dump);

            //para cuando es la ultima excepcion restante y se elimina no qde { }
            $yaml_new = $this->getExceptionByDirYAML($dirGeneral);
            if (empty($yaml_new['excepciones'])) {
                $yaml_new['excepciones'] = null;
                $yaml_dump = $dumper->dump($yaml_new, 6);
                file_put_contents($dirGeneral, $yaml_dump);
            }
        } elseif ($extGeneral == 'xml') {
            $xml = $this->getExceptionByDirXML($dirGeneral);
            unset($xml[$codigoExcepcion]);
            //return new JsonResponse($xml);

            //re-escribiendo en el xml:
            $rootNode = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><excepciones></excepciones>");

            foreach ($xml as $key => $value) {
                $itemNode = $rootNode->addChild($key);
                $itemNode->addChild('mensaje', $value['mensaje']);
                $itemNode->addChild('descripcion', $value['descripcion']);
                if (array_key_exists('show_in_prod', $value)) {
                    if ($value['show_in_prod'] == "true") {
                        $itemNode->addChild('show_in_prod', "true");
                    }
                }
            }
            $rootNode->asXML($dirGeneral);

        }
        //ahora borrando la excepcion de los ficheros translate
        $rutaTranslations = $this->getDirCarpetaTraslationByBundle($bundle);
        $arrayFileTrans = scandir($rutaTranslations);

        foreach ($arrayFileTrans as $fichero) {
            if (strpos($fichero, 'translatesexcepciones') !== false) {
                $yaml = $this->getExceptionByDirYAML($rutaTranslations . $fichero);
                if (!empty($yaml['excepciones'][$codigoExcepcion])) {
                    unset($yaml['excepciones'][$codigoExcepcion]);
                    $yaml_dump = $dumper->dump($yaml, 6);
                    file_put_contents($rutaTranslations . $fichero, $yaml_dump);
                    //para cuando es la ultima excepcion restante y se elimina no qde { }
                    $yaml_new = $this->getExceptionByDirYAML($rutaTranslations . $fichero);
                    if (empty($yaml_new['excepciones'])) {
                        $yaml_new['excepciones'] = null;
                        $yaml_dump = $dumper->dump($yaml_new, 6);
                        file_put_contents($rutaTranslations . $fichero, $yaml_dump);
                    }
                }
            }
        }
        return new Response();
    }

    /**
     * Modifica una excepción
     * Responde al RF(80) Modificar excepción por componente
     * @Route(path="/excepciones/modifyException", name="exception_modify_data", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function ModificarExcepcion(Request $request)
    {
        $dumperyml = new Dumper();

        $data = new Data();
        $formData = $this->createForm(new DataType(), $data);
        $formData->handleRequest($request);

        $dir = $this->getDirGeneralByBundle($data->getBundle());
        $extensionGeneral = $this->getExtensionGeneralByBundle($data->getBundle());

        if ($data->getCodigo() !== $data->getCodigoAnterior()) {
            if ($this->existException($dir, $extensionGeneral, $data->getCodigo())) {
                return new Response("El nuevo código especificado está en uso por otra excepción", 500);
            }
        }

        //si no esta siendo utilizado el nuevo codigo, elimino usando el anterior
        //luego adiciono con el nuevo
        $this->BorrarExcepcion($data->getBundle(), $data->getCodigoAnterior());

        //adicionando
        $listTranslation = $data->getListTranslation();
        if ($extensionGeneral == 'yml') {
            $yaml = $this->getExceptionByDirYAML($dir);
            if ($formData->isValid()) {
                $yaml['excepciones'][$data->getCodigo()]['mensaje'] = 'excepciones.' . $data->getCodigo() . '.mensaje';  //mensaje aleatorio
                $yaml['excepciones'][$data->getCodigo()]['descripcion'] = 'excepciones.' . $data->getCodigo() . '.descripcion';;  //mensaje aleatorio
                if ($data->getShowprod() == "true") {
                    $yaml['excepciones'][$data->getCodigo()]['show_in_prod'] = true;
                }
                $yaml_dump = $dumperyml->dump($yaml, 6);
                file_put_contents($dir, $yaml_dump);
            }
        } elseif ($extensionGeneral == 'xml') {
            $xml = $this->getExceptionByDirXML($dir);
            if ($formData->isValid()) {
                $xml[$data->getCodigo()]['mensaje'] = 'excepciones.' . $data->getCodigo() . '.mensaje';
                $xml[$data->getCodigo()]['descripcion'] = 'excepciones.' . $data->getCodigo() . '.descripcion';
                if ($data->getShowprod() == "true") {
                    $xml[$data->getCodigo()]['show_in_prod'] = true;
                }

                //re-escribiendo en el xml:
                $rootNode = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><excepciones></excepciones>");

                foreach ($xml as $key => $value) {
                    $itemNode = $rootNode->addChild($key);
                    $itemNode->addChild('mensaje', $value['mensaje']);
                    $itemNode->addChild('descripcion', $value['descripcion']);
                    if (array_key_exists('show_in_prod', $value)) {
                        if ($value['show_in_prod'] == "true") {
                            $itemNode->addChild('show_in_prod', "true");
                        }
                    }
                }
                $rootNode->asXML($dir);
            }
        }

        //ahora con los translations q son todos en yml por suerte (porq lo son no ?!)
        foreach ($listTranslation as $t) {
            $dirTraslation = $this->getDirTraslationByBundleIdioma($data->getBundle(), $t['idioma']);
            $yaml = $this->getExceptionByDirYAML($dirTraslation); //porq solo es yml
            $yaml['excepciones'][$data->getCodigo()]['mensaje'] = $t['mensaje'];
            $yaml['excepciones'][$data->getCodigo()]['descripcion'] = $t['descrip'];
            $yaml_dump = $dumperyml->dump($yaml, 6);
            file_put_contents($dirTraslation, $yaml_dump);
        }

        return new Response();
    }
}


