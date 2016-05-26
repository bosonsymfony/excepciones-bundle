<?php

namespace UCI\Boson\ExcepcionesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ExcepcionesBundle
 *
 * @author Daniel Arturo Casals Amat <dacasals@uci.cu>
 * @package UCI\Boson\ExcepcionesBundle
 */
class ExcepcionesBundle extends Bundle
{
    public function getParent()
    {
         return 'TwigBundle';
    }

}