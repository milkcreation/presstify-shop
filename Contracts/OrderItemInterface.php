<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface OrderItemInterface extends ParamsBag
{
    /**
     * Récupération de l'identifiant de qualification.
     * {@internal Identifiant de l'élément enregistré en base de données.}
     *
     * @return int
     */
    public function getId();

    /**
     * Récupération d'une metadonnée d'élement associé à la commande
     *
     * @param string $meta_key Clé d'index de la métadonnée à récupérer.
     * @param bool $single Type de récupération. single|multi.
     *
     * @return mixed
     */
    public function getMeta($meta_key, $single = true);

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de l'identifiant de qualification de la commande.
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Récupération du type d'élement associé à la commande
     *
     * @return string coupon|fee|line_item|shipping|tax
     */
    public function getType();
}