<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;
use tiFy\Plugins\Shop\Contracts\ProductItemInterface;

interface ProductPurchasingOption extends ParamsBag
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();

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
     * Vérification d'activation de l'option d'achat.
     *
     * @return boolean
     */
    public function isActive();

    /**
     * Affichage
     *
     * @return string
     */
    public function render();

    /**
     * Définition de la valeur de selection.
     *
     * @return void
     */
    public function setSelected($selected);
}