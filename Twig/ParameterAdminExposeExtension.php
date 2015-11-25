<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16/10/14
 * Time: 12:54
 */
namespace UCI\Boson\ExcepcionesBundle\Twig;

class ParameterAdminExposeExtension extends  \Twig_Extension {

    private  $excepcionesParams;

    function __construct($excepcionesParams)
    {
        $this->excepcionesParams = $excepcionesParams;
    }


    public function getGlobals()
    {
        return array(
            'admin_contact' => $this->excepcionesParams['email_admin_contact']
        );
    }


    public function getName()
    {
        return 'parameter_admin_expose_extension';    }

} 