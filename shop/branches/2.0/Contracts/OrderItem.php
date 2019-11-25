<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use Illuminate\Database\Query\Builder;
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

    /**
     * Récupération de l'instance de la commande associée.
     *
     * @return Order
     */
    public function order(): Order;

    /**
     * Sauvegarde des données de l'élément de commande en base.
     *
     * @return int Identifiant de qualification en base.
     */
    public function save(): int;

    /**
     * Sauvegarde des metadonnées de l'élément de commande en base.
     *
     * @return int[] Liste des identifiant de qualification des métadonnées en base.
     */
    public function saveMetas(): array;

    /**
     * Sauvegarde d'une metadonnée de l'élément de commande en base.
     *
     * @param string $key Clé d'indice de la métadonnée.
     * @param mixed $value Valeur de la métadonnée.
     *
     * @return int Identifiant de qualification de la métadonnée en base.
     */
    public function saveMeta(string $key, $value): int;

    /**
     * Récupération d'une instance du gestionnaire de requête des éléments associés à une commande.
     *
     * @return Builder
     */
    public function table(): Builder;

    /**
     * Récupération d'une instance du gestionnaire de requête des métadonnées d'éléments associés à une commande.
     *
     * @return Builder
     */
    public function tableMeta(): Builder;
}