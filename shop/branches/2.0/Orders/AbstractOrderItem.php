<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use BadMethodCallException;
use Exception;
use tiFy\Plugins\Shop\Contracts\{Order, OrderItem as OrderItemContract};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\{ParamsBag, Str};
use tiFy\Support\Proxy\Database;

/**
 * @mixin OrderItem
 */
abstract class AbstractOrderItem extends ParamsBag implements OrderItemContract
{
    use ShopAwareTrait;

    /**
     * Cartographie des attributs en correspondance avec les données enregistrées en base.
     * @var array
     */
    protected $datasMap = [
        'id'       => 'order_item_id',
        'name'     => 'order_item_name',
        'type'     => 'order_item_type',
        'order_id' => 'order_id',
    ];

    /**
     * Cartographie des attributs en correspondance avec les métadonnées enregistrées en base.
     * @var array
     */
    protected $metasMap = [];

    /**
     * Liste des métadonnées enregistrées en base.
     * @var array|null
     */
    protected $metas;

    /**
     * Instance de la commande associée.
     * @var Order
     */
    protected $order;

    /**
     * CONSTRUCTEUR.
     *
     * @param Order $order
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;

        $this->setShop($order->shop());
    }

    /**
     * @inheritDoc
     */
    public function __call(string $name, array $arguments)
    {
        try {
            return $this->shop()->resolve('order.item', [$this->order])->$name(...$arguments);
        } catch (Exception $e) {
            throw new BadMethodCallException(sprintf(__('La méthode %s n\'est pas disponible.', 'tify'), $name));
        }
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'id'       => 0,
            'name'     => '',
            'order_id' => 0,
            'type'     => ''
        ];
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return (int)$this->get('id', 0);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->get('name', '');
    }

    /**
     * @inheritDoc
     */
    public function getMeta(?string $key = null, $default = null)
    {
        if (is_null($this->metas)) {
            $this->metas = [];

            $queryMetas = Database::table('tify_shop_order_itemmeta')->where('order_item_id', $this->getId())->get();

            if ($queryMetas->count()) {
                foreach ($queryMetas as $q) {
                    $value = $q->meta_value;
                    $this->metas[$q->meta_key] = is_string($value) ? Str::unserialize($value) : $value;
                }
            }
        }

        return is_null($key) ? $this->metas : ($this->metas[$key] ?? $default);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId(): int
    {
        return (int)$this->get('id', 0);
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return (string)$this->get('type', '');
    }

    /**
     * @inheritDoc
     */
    public function mapData($key, ?string $mapKey = null): OrderItemContract
    {
        $keys = is_array($key) ? $key : [$key => $mapKey];

        foreach($keys as $key => $mapKey) {
            if ($value = $this->pull($mapKey)) {
                $this->set($key, $value);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function mapMeta($key, ?string $mapKey = null): OrderItemContract
    {
        $keys = is_array($key) ? $key : [$key => $mapKey];

        foreach($keys as $key => $mapKey) {
            if ($value = $this->getMeta($mapKey, null)) {
                $this->set($key, $value);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parse(): OrderItemContract
    {
        parent::parse();

        $this->mapData($this->datasMap);

        if (!$id = $this->getId()) {
            return $this;
        }

        $this->mapMeta($this->metasMap);

        return $this;
    }
}