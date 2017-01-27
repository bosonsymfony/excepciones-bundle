<?php

namespace UCI\Boson\ExcepcionesBundle\Tests\DependencyInjection;

use UCI\Boson\ExcepcionesBundle\DependencyInjection\ExcepcionesExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExcepcionesExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \UCI\Boson\ExcepcionesBundle\DependencyInjection\ExcepcionesExtension
     */
    private $extension;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    protected function setUp()
    {
        $this->extension = new ExcepcionesExtension(__DIR__.'/../../',"ExcepcionesBundle");
        $this->container = new ContainerBuilder();
    }

    protected function tearDown()
    {
        $this->extension = null;
        $this->container = null;
    }

    public function testEmptyConfigUsesDefaultValuesAndServicesAreCreated()
    {   $this->container->setParameter('excepciones', array('email_admin_contact'=> 'dacasals@uci.cu'));
        $this->extension->load(array(),$this->container);
        $this->assertTrue($this->container->hasParameter('excp_excepciones_bundle'));
        $this->assertTrue( array_key_exists('excepciones',$this->container->getParameter('excp_excepciones_bundle')));
        $this->assertTrue($this->container->has('kernel.excepciones.listener'));

    }
}