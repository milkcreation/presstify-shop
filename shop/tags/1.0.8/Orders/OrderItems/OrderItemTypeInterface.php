<?php

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\Plugins\Shop\Orders\OrderInterface;

interface OrderItemTypeInterface
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
     * Récupération de l'identifiant de qualification.
     * @internal Identifiant de l'élément en base de données.
     * @return int
     */
    public function getId();

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
     * Récupération de l'identifiant de qualification de la commande.
     * @return int
     */
    public function getOrderId();

    /**
     * Récupération de la classe de rappel de la commande associée.
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * Vérification de validité du type d'élement.
     * @param string $type
     * @return boolean
     */
    public function isType($type);

    /**
     * Enregistrement de l'élément.
     * @return int
     */
    public function save();

    /**
     * Enregistrement de la liste des métadonnées cartographiées.
     * @return void
     */
    public function saveMetas();

    /**
     * Enregistrement d'une métadonnée
     * @param string $meta_key Clé d'identification de la métadonnée.
     * @param mixed $meta_value Valeur de la métadonnée
     * @param bool $unique Enregistrement unique d'une valeur pour la clé d'identification fournie.
     * @return int Valeur de la clé primaire de la métadonnée enregistrée.
     */
    public function saveMeta($meta_key, $meta_value, $unique = true);
}