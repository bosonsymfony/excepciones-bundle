<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UCI\Boson\ExcepcionesBundle\Controller;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UCI\Boson\ExcepcionesBundle\Loader\XmlFileReader;
use UCI\Boson\ExcepcionesBundle\Loader\YamlFileReader;

/**
 * ExceptionController.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ExceptionController extends \Symfony\Bundle\TwigBundle\Controller\ExceptionController
{


    /**
     * Converts an Exception to a Response.
     *
     * @param Request              $request   The request
     * @param FlattenException     $exception A FlattenException instance
     * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
     *
     * @return Response
     *
     * @throws \InvalidArgumentException When the exception template does not exist
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));

        $code = $exception->getStatusCode();
        if($exception->GetClass() == 'UCI\Boson\ExcepcionesBundle\Exception\LocalException'){
            $arrayRutaFile = explode(DIRECTORY_SEPARATOR, $exception->getFile());
            $strExtraer = "";
            for ($i = count($arrayRutaFile) -1 ; $i >=0 ; $i--) {
                if (preg_match('/[a-zA-Z0-9]Bundle$/', $arrayRutaFile[$i]) == 1) {
                    $direccionBundle = explode($strExtraer,$exception->getFile())[0];
                    $direccionFileExcepciones = $direccionBundle  . DIRECTORY_SEPARATOR ."Resources".DIRECTORY_SEPARATOR."config";
                    if(is_dir($direccionFileExcepciones))
                        break;
                }
                $strExtraer =DIRECTORY_SEPARATOR.$arrayRutaFile[$i].$strExtraer;
            }
            $showInProd = false;
            $values = $this->getArrayExcepcionesInFile($direccionFileExcepciones);
            foreach($values as $key =>$excp){
                if($key == $exception->getCode() && array_key_exists('show_in_prod',$excp)){
                    $showInProd = $excp['show_in_prod'];
                }
            }



            return new Response($this->twig->render(
                (string) $this->findTemplate($request, $request->getRequestFormat(), $code, $this->debug),
                array(
                    'status_code' => $code,
                    'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                    'exception' => $exception,
                    'show_in_prod' => $showInProd,
                    'logger' => $logger,
                    'currentContent' => $currentContent,
                )
            ));
        }
        return new Response($this->twig->render(
            (string) $this->findTemplate($request, $request->getRequestFormat(), $code, $this->debug),
            array(
                'status_code' => $code,
                'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                'exception' => $exception,
                'logger' => $logger,
                'currentContent' => $currentContent,
            )
        ));
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


}
