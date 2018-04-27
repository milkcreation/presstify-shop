<?php

namespace tiFy\Plugins\Shop\Products;

interface ProductPurchasingOptionInterface
{
    /**
     * Vérification d'existance.
     *
     * @return bool
     */
    public function exists();

    /**
     * Récupération d'attribut de configuration.
     *
     * @param string $key Clé d'index de l'attribut de configuration. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut.
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Identifiant de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Intitulé de qualification.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Intitulé de qualification.
     *
     * @return array
     */
    public function getValueList();
}