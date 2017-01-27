<?php

namespace UCI\Boson\ExcepcionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Data
 */
class Data
{

    /**
     * @var string
     *
     */
    private $codigo;

    /**
     * @var string
     *
     */
    private $bundle;


    /**
     * @var string
     *
     */
    private $showprod;


    /**
     * @var string
     *
     */
    private $codigoAnterior;


    /**
     * @var mixed
     *
     */
    private $listTranslation;


    /**
     * @return string
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * @param string $bundle
     */
    public function setBundle($bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Data
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @return string
     */
    public function getShowprod()
    {
        return $this->showprod;
    }

    /**
     * @param string $showprod
     */
    public function setShowprod($showprod)
    {
        $this->showprod = $showprod;
    }

    /**
     * Set listTranslation
     *
     * @param mixed $listTranslation
     *
     * @return Data
     */
    public function setListTranslation($listTranslation)
    {
        $this->listTranslation = $listTranslation;

        return $this;
    }

    /**
     * Get listTranslation
     *
     * @return mixed
     */
    public function getListTranslation()
    {
        return $this->listTranslation;
    }

    /**
     * @return string
     */
    public function getCodigoAnterior()
    {
        return $this->codigoAnterior;
    }

    /**
     * @param string $codigoAnterior
     */
    public function setCodigoAnterior($codigoAnterior)
    {
        $this->codigoAnterior = $codigoAnterior;
    }
}

