<?php

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use Illuminate\Support\Fluent;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;

interface OrderItemInterface
{
    /**
     * Récupération d'une donnée d'élement associé à la commande.
     * @param string $key Clé d'indice de la donnée à récupérer.
     * @param mixed $default Valeur de retour par défaut.
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Récupération d'une metadonnée d'élement associé à la commande.
     * @param string $meta_key Clé d'index de la métadonnée à récupérer.
     * @param bool $single Type de récupération. single|multi.
     * @return mixed
     */
    public function getMeta($meta_key, $single = true);

    /**
     * Récupération de l'identifiant de qualification.
     * @internal Identifiant de l'élément en base de données.
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
     * Récupération du type d'élement associé à la commande
     * @return string coupon|fee|line_item|shipping|tax
     */
    public function getType();

    /**
     * Récupération de l'identifiant de qualification de la commande.
     * @return int
     */
    public function getOrderId();
}