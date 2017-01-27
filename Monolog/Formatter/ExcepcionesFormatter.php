<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/29/14
 * Time: 3:52 p.m.
 */
namespace UCI\Boson\ExcepcionesBundle\Monolog\Formatter;

use Monolog\Formatter\NormalizerFormatter;

/**
 * Establece el formato de los records del log a escribir facilitando su comprensión.
 * Responde al RF (58)(Configurar y registrar  datos  de las excepciones)

 *
 * @author Daniel Arturo Casals <dacasals@uci.cu>
 */
class ExcepcionesFormatter extends NormalizerFormatter
{
    const SIMPLE_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";

    protected $format;
    protected $allowInlineLineBreaks;

    /**
     * @param string $format                El formato del mensaje
     * @param string $dateFormat            El formato de tipo timestamp: uno de los admitidos por el formato DateTime
     */
    public function __construct($format = null, $dateFormat = null)
    {
        $this->format = $format ?: static::SIMPLE_FORMAT;
        parent::__construct($dateFormat);
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        $vars = parent::format($record);
        $output = "###############################################################\n###############################################################\n";

        $output = $output.$this->format;
        $valores = "";
        foreach ($vars['context'] as $var => $val) {

               $valores =$valores ."  ". $var. ": ".$val."\n";
        }
        $output = str_replace('%context%', $valores, $output);
        foreach ($vars as $var => $val) {
            if (false !== strpos($output, '%'.$var.'%')) {
                $output = str_replace('%'.$var.'%', $this->convertToString($val), $output);
            }
        }

        return $output;
    }

    /**
     * Retorna un objeto convertido en string para ser incluido en la salida
     *
     * @param $data
     * @return mixed|string
     */
    protected function convertToString($data)
    {
        if (null === $data || is_bool($data)) {
            return var_export($data, true);
        }

        if (is_scalar($data)) {
            return (string) $data;
        }

        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return $this->toJson($data, true);
        }

        return str_replace('\\/', '/', @json_encode($data));
    }

}