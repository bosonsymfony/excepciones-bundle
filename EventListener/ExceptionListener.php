<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/09/14
 * Time: 13:38
 */

namespace UCI\Boson\ExcepcionesBundle\EventListener;

use UCI\Boson\ExcepcionesBundle\Exception\LocalException;
use UCI\Boson\ExcepcionesBundle\Monolog\Formatter\ExcepcionesFormatter;
use UCI\Boson\ExcepcionesBundle\Monolog\Formatter\ExcepcionesLineFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;


/**
 *
 * Class ExceptionListener, escucha los eventos de tipo kernel::exception.
 *
 * @author Daniel Arturo Casals Amat <dacasals@uci.cu>
 * @package UCI\Boson\ExcepcionesBundle\EventListener
 */
class ExceptionListener
{
    private $container;
    private $rootLogsDir;
    private $fileName;
    function __construct( ContainerInterface $container, $rootLogsDir, $fileName = 'excepciones.log')
    {
        $this->container = $container;
        $this->rootLogsDir = $rootLogsDir;
        $this->fileName = $fileName;
    }

    /**
     * Captura los eventos generados por las excepciones y
     * escribe en el fichero logs/excepciones.log información de cada una de ellas.
     * Responde al RF (58)(Configurar y registrar  datos  de las excepciones)
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $logger = new Logger('excepciones');
        $stream = new StreamHandler($this->rootLogsDir.$this->fileName, Logger::DEBUG);

        $output = "[%datetime%]: %message% %context% \n";
        $stream->setFormatter(new ExcepcionesFormatter($output));
        $logger->pushHandler($stream);
        if ($exception instanceof LocalException) {
            $logger->addInfo("LocalException",
                array(
                    "codigo" =>$exception->getCodigo(),
                    "mensaje" =>$exception->getMensaje(),
                    "descripcion" =>$exception->getDescripcion(),
                    "clase" => $exception->getClase(),
                    "linea" =>$exception->getLinea(),
                    "metodo" =>$exception->getMetodo(),
                    "traza" => $exception->getTraceAsString()
                ));
        }
        else {
            $trazas = $exception->getTrace();

            $logger->addInfo(get_class($exception),
                array(
                    "codigo" =>$exception->getCode(),
                    "mensaje" =>$exception->getMessage(),
                    "clase" => $exception->getFile(),
                    "linea" =>$exception->getLine(),
                    "metodo" =>$trazas[0]['function'],
                    "traza" => $exception->getTraceAsString(),
                ));
        }
    }
} 