<?php

namespace tiFy\Plugins\Shop\Orders\OrderItem;

use tiFy\Plugins\Shop\Orders\OrderInterface;

interface OrderItemInterface
{
    /**
     * Récupération de la valeur d'un attribut.
     * @param string $key Identifiant de qualification de l'attribut
     * @param mixed $default Valeur de retour par défaut
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Définition de la valeur d'un attribut.
     * @param string $key Identifiant de qualification de l'attribut
     * @param mixed $value Valeur de définition de l'attribut
     * @return self
     */
    public function set($key, $value);

    /**
     * Récupération de la liste des attributs
     * @return array
     */
    public function all();

    /**
     * Enregistrement de l'élément.
     * @return int
     */
    public function save();

    /**
     * Récupération de la classe de rappel de la commande associée.
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * Récupération de l'identifiant de qualification de la commande.
     * @return int
     */
    public function getOrderId();

    /**
     * Récupération de l'intitulé de qualification.
     * @return string
     */
    public function getName();

    /**
     * Récupération du type d'élement associé à la commande
     * @return string coupon|fee|line_item|shipping|tax
     */
    public function getType();

    /**
     * Vérification de validité du type d'élement.
     * @param string $type
     * @return boolean
     */
    public function isType($type);
}