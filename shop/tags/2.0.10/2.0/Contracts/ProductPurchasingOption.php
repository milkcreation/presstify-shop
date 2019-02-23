<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface ProductPurchasingOption extends ParamsBag
{
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
     * @return mixed
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
     * Affichage d'une ligne de panier.
     *
     * @return string
     */
    public function renderCartLine();

    /**
     * Affichage du champ de saisie.
     *
     * @return string
     */
    public function renderProduct();

    /**
     * Définition de la valeur de selection.
     *
     * @param $selected
     *
     * @return void
     */
    public function setSelected($selected);
}