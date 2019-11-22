<?php declare(strict_types=1);

namespace __tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface OrderItemType extends ParamsBag
{
    /**
     * Récupération de l'identifiant de qualification.
     * @internal Identifiant de l'élément en base de données.
     *
     * @return int
     */
    public function getId();

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de la classe de rappel de la commande associée.
     *
     * @return Order
     */
    public function getOrder();

    /**
     * Récupération de l'identifiant de qualification de la commande.
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Récupération du type d'élement associé à la commande.
     *
     * @return string
     */
    public function getType();

    /**
     * Vérification de validité du type d'élement.
     *
     * @param string $type
     *
     * @return boolean
     */
    public function isType($type);

    /**
     * Enregistrement de l'élément.
     *
     * @return int
     */
    public function save();

    /**
     * Enregistrement de la liste des métadonnées cartographiées.
     *
     * @return void
     */
    public function saveMetas();

    /**
     * Enregistrement d'une métadonnée.
     *
     * @param string $meta_key Clé d'identification de la métadonnée.
     * @param mixed $meta_value Valeur de la métadonnée
     * @param bool $unique Enregistrement unique d'une valeur pour la clé d'identification fournie.
     *
     * @return int Valeur de la clé primaire de la métadonnée enregistrée.
     */
    public function saveMeta($meta_key, $meta_value, $unique = true);

    /**
     * Définition de la liste des données de l'élément enregistrées en base de données.
     *
     * @param OrderItem $item Classe de rappel des données de l'élément en base.
     *
     * @return void
     */
    public function setDatas(OrderItem $item);

    /**
     * Définition de la liste des metadonnées de l'élément enregistrées en base de données.
     *
     * @param OrderItem $item Classe de rappel des données de l'élément en base.
     *
     * @return void
     */
    public function setMetas(OrderItem $item);
}