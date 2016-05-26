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

use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\TemplateReferenceInterface;
use UCI\Boson\ExcepcionesBundle\Exception\LocalException;

/**
 * ExceptionController.
 *
 * @author Daniel Arturo Casals Amat <dacasals@uci.cu>
 */
class ExceptionController extends \Symfony\Bundle\TwigBundle\Controller\ExceptionController implements ContainerAwareInterface
{
    public function __construct(\Twig_Environment $twig, $debug,ContainerInterface $container = null)
    {
        parent::__construct($twig, $debug);
        $this->container = $container;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


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
            $arrayDirFile = explode(DIRECTORY_SEPARATOR,$exception->getFile());
            $show_in_prod = false;
            foreach ($arrayDirFile as $item) {
                if (preg_match('/[a-zA-Z]+Bundle$/', $item) !== 0) {
                    $arrayParamsExcep = $this->container->getParameter("excp_" . ContainerBuilder::underscore($item))["excepciones"][$exception->getCode()];
                    if (array_key_exists("show_in_prod", $arrayParamsExcep)) {
                        $show_in_prod = $arrayParamsExcep["show_in_prod"];
                    }
                    break;
                }
            }
            return new Response($this->twig->render(
                (string) $this->findTemplate($request, $request->getRequestFormat(), $code, $this->debug),
                array(
                    'status_code' => $code,
                    'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                    'exception' => $exception,
                    'show_in_prod' => $show_in_prod,
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
}
