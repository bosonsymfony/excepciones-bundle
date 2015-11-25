<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UCI\Boson\ExcepcionesBundle\Loader;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\Yaml\Parser as YamlParser;
use Symfony\Component\Config\Loader\FileLoader;
/**
 * Se encarga de cargar la información definida en los ficheros de configuración en formato YAML
 *
 * The YAML format does not support anonymous services (cf. the XML loader).
 *
 * @author Daniel Arturo Casals Amat <dacasals@uci.cu>
 */
class YamlFileReader extends FileLoader
{
    /**
     * @var
     */
    private $yamlParser;


    /**
     * Lee el contenido de un fichero Yaml
     *
     * @param mixed $file
     * @param null $type
     * @return array
     */
    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        $content = $this->loadFile($path);
        // empty file

        return $content;
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param mixed  $resource A resource
     * @param string $type     The resource type
     *
     * @return bool    true if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo($resource, PATHINFO_EXTENSION);
    }


    /**
     * Loads a YAML file.
     *
     * @param string $file
     *
     * @return array The file content
     *
     * @throws InvalidArgumentException when the given file is not a local file or when it does not exist
     */
    protected function loadFile($file)
    {
        if (!stream_is_local($file)) {
            throw new InvalidArgumentException(sprintf('This is not a local file "%s".', $file));
        }

        if (null === $this->yamlParser) {
            $this->yamlParser = new YamlParser();
        }

        return $this->validate($this->yamlParser->parse(@file_get_contents($file)), $file);
    }

    /**
     * Validates a YAML file.
     *
     * @param mixed  $content
     * @param string $file
     *
     * @return array
     *
     * @throws InvalidArgumentException When service file is not valid
     */
    private function validate($content, $file)
    {
        if (null === $content) {
            return $content;
        }

        if (!is_array($content)) {
            throw new InvalidArgumentException(sprintf('The exceptions file "%s" is not valid.', $file));
        }


        return $content;
    }


}
