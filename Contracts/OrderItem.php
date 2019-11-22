<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\ParamsBag;
use tiFy\Plugins\Shop\Orders\OrderItem as DelegateOrderItem;

/**
 * @mixin DelegateOrderItem
 */
interface OrderItem extends ParamsBag, ShopAwareTrait
{
    /**
     * Appel des méthodes de la classe par délégation.
     *
     * @param string $name Nom de qualification de la méthode.
     * @param array $arguments Liste des arguments passés à la méthode.
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments);

    /**
     * Récupération de l'identifiant de qualification.
     * {@internal Identifiant de l'élément enregistré en base de données.}
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération de la liste des metadonnée|d'une metadonnée associé à l'élément de la commande.
     *
     * @param string|null $key Clé d'index de la métadonnée à récupérer.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getMeta(?string $key = null, $default = null);

    /**
     * Récupération de l'identifiant de qualification de la commande.
     *
     * @return int
     */
    public function getOrderId(): int;

    /**
     * Récupération du type d'élement associé à la commande
     *
     * @return string coupon|fee|line_item|shipping|tax
     */
    public function getType(): string;

    /**
     * Définition de cartographie des données principales.
     *
     * @param string|array $key Clé d'indice de la donnée|Liste de données à cartographier.
     * @param string|null $mapKey Nom de qualification de la clé d'indice mappée.
     *
     * @return static
     */
    public function mapData($key, ?string $mapKey = null): OrderItem;

    /**
     * Définition de cartographie des métadonnées.
     *
     * @param string|array $key Clé d'indice de la donnée|Liste de données à cartographier.
     * @param string|null $mapKey Nom de qualification de la clé d'indice mappée.
     *
     * @return static
     */
    public function mapMeta($key, ?string $mapKey = null): OrderItem;
}