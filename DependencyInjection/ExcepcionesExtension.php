<?php

namespace UCI\Boson\ExcepcionesBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use UCI\Boson\ExcepcionesBundle\Loader\YamlFileLoader;
use UCI\Boson\ExcepcionesBundle\Loader\XmlFileLoader;
use UCI\Boson\AspectBundle\DependencyInjection\AspectExtension;

/**
 * Esta clase administra las configuraciones para este bundle. Para aprender mas ver  {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @author Daniel Arturo Casals Amat <dacasals@uci.cu>
 */
class ExcepcionesExtension extends Extension
{
    private $direccionBundle;
    private $nameBundle;

    function __construct($direccionBundle = "",$nameBundle = "")
    {
        $this->direccionBundle = $direccionBundle;
        $this->nameBundle = $nameBundle;
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter("excepciones",array('email_admin_contact'=>$config['email_admin_contact']));
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__. '/../Resources/config'));
        $loader->load('services.yml');
        $this->loadFileExcepciones($container);
    }

    /**
     * Se carga la configuración definida en los ficheros  Resources/config/excepciones.[yml, xml] como configuraciones
     * globales del sistema, accesibles desde el contenedor de servicios.
     *
     * @param ContainerBuilder $container
     *
     * @throws \InvalidArgumentException Cuando no existe ningun  fichero mombrado  excepciones.[yml, xml].
     */
    public function loadFileExcepciones(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        foreach ($bundles as $key => $bundle) {
            $refClass = new \ReflectionClass($bundle);
            $bundleDir = dirname($refClass->getFileName());
            $DirConfig = $bundleDir . '/Resources/config';
            $locator = new FileLocator($DirConfig);
            try {
                if (file_exists($DirConfig . DIRECTORY_SEPARATOR . "excepciones.yml")) {
                    $loader = new YamlFileLoader($container, $locator);

                    $locator->locate("excepciones.yml");
                    $configs = $loader->load('excepciones.yml');
                } else if (file_exists($DirConfig . DIRECTORY_SEPARATOR . "excepciones.xml")) {
                    $loader = new XmlFileLoader($container, $locator);
                    $locator->locate("excepciones.xml");
                    $configs["excepciones"] = $loader->load('excepciones.xml');
                } else {
                    continue;
                }

            } catch (\InvalidArgumentException $exc) {
                throw $exc;
            }
            $configuration = new ExcepcionesConfiguration();

            $config = $this->processConfiguration($configuration, array('excepciones' => $configs));
            $arrayNSBundle = explode("\\",$bundle);
            $container->setParameter("excp_" . $container::underscore($arrayNSBundle[count($arrayNSBundle)-2]), $config);
        }

    }

    /**
     * Modifica el atributo $direccionBundle de esta clase con la dirección del Bundle actual en que se instancia el objeto.
     *
     * @param $container
     */
    private function getDirBundle($container){
        $resources =  $container->getResources();
        $arrayRutaFile = explode(DIRECTORY_SEPARATOR,$resources[0]);
        $this->direccionBundle = $arrayRutaFile[0];
        for ($i = 1; $i < count($arrayRutaFile); $i++) {
            $this->direccionBundle = $this->direccionBundle . DIRECTORY_SEPARATOR . $arrayRutaFile[$i];
            if (preg_match('/Bundle$/',$arrayRutaFile[$i]) == 1) {
                $this->nameBundle = $arrayRutaFile[$i];
                break;
            }
        }
    }
}
