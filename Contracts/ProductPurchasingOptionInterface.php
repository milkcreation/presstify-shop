<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;
use tiFy\Plugins\Shop\Contracts\ProductItemInterface;

interface ProductPurchasingOptionInterface extends ParamsBag
{
    /**
     * Récupération d'attribut.
     *
     * @param string $key Clé d'index de l'attribut de configuration. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut.
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Intitulé de qualification.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Identifiant de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Classe de rappel du produit associé.
     *
     * @return null|ProductItemInterface
     */
    public function getProduct();

    /**
     * Récupération de la valeur de selection.
     *
     * @param mixed $default Valeur de retour par défaut
     *
     * @return void
     */
    public function getValue($default = null);

    /**
     * Intitulé de qualification.
     *
     * @return array
     */
    public function getValueList();

    /**
     * Vérification d'existance.
     *
     * @return bool
     */
    public function isActive();

    /**
     * Définition de la valeur de selection.
     *
     * @return void
     */
    public function setSelected($selected);
}