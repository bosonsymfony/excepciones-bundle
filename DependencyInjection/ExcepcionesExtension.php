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
        try{
            $configs['excepciones'] = $container->getParameter('excepciones');
        }
        catch(\Exception $e){}
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__. '/../Resources/config'));
        $loader->load('services.yml');
        $this->loadFileExcepciones($container,__DIR__);
    }

    /**
     * Se carga la configuración definida en los ficheros  Resources/config/excepciones.[yml, xml] como configuraciones
     * globales del sistema, accesibles desde el contenedor de servicios.
     *
     * @param ContainerBuilder $container
     * @param string $direccion Dirección hasta la Carpeta DependencyInjection
     *
     * @throws \InvalidArgumentException Cuando no existe ningun  fichero mombrado  excepciones.[yml, xml].
     */
    public function loadFileExcepciones(ContainerBuilder $container,$dirección)
    {
        if($this->direccionBundle == "" || $this->nameBundle == ""  )
            $this->getDirBundle($container,$dirección);

        $locator = new FileLocator($this->direccionBundle . '/Resources/config');
        try {
            $loader = new YamlFileLoader($container, $locator);

            $locator->locate("excepciones.yml");
            $configs = $loader->load('excepciones.yml');

        } catch (\InvalidArgumentException $exc) {
            try {
                $loader = new XmlFileLoader($container, $locator);
                $locator->locate("excepciones.xml");
                $configs["excepciones"] = $loader->load('excepciones.xml');

            } catch (\InvalidArgumentException $exc) {
                throw $exc;
            }
        }
        $configuration = new ExcepcionesConfiguration();

        $config = $this->processConfiguration($configuration, array('excepciones'=>$configs));
        $container->setParameter("excp_" . $container::underscore($this->nameBundle), $config);
    }

    /**
     * Modifica el atributo $direccionBundle de esta clase con la dirección del Bundle actual en que se instancia el objeto.
     *
     * @param $container
     * @param string $dir Dirección hasta DedendencyInjection del bundle a obtener la dirección.
     */
    private function getDirBundle(ContainerBuilder $container, $dir){
        $resources =  $container->getResources();
        foreach ($resources as $resource) {
            if(strpos($resource,$dir) !== false){
                $this->direccionBundle = substr($dir,0,strlen($dir)- 20);
                $arrayDirBundleSplited = explode(DIRECTORY_SEPARATOR, $this->direccionBundle);
                $this->nameBundle = $arrayDirBundleSplited[count($arrayDirBundleSplited) -1];
                break;
            }
        }
    }
}
