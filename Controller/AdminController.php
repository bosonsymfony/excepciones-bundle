<?php

namespace UCI\Boson\ExcepcionesBundle\Controller;

use UCI\Boson\BackendBundle\Controller\BackendController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminController extends BackendController
{
    /**
     * @Route(path="/excepciones/admin/scripts/config.excepciones.js", name="excepciones_app_config")
     */
    public function getAppAction()
    {
        return $this->jsResponse('ExcepcionesBundle:Scripts:config.js.twig');
    }

}
