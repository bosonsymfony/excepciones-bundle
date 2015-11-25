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
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\TemplateReferenceInterface;
use UCI\Boson\ExcepcionesBundle\Exception\LocalException;

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
            $local = new LocalException($exception->getCode());
            return new Response($this->twig->render(
                (string) $this->findTemplate($request, $request->getRequestFormat(), $code, $this->debug),
                array(
                    'status_code' => $code,
                    'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                    'exception' => $exception,
                    'show_in_prod' => $local->getShowInProd(),
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
