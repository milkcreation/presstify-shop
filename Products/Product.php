<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Products;

use tiFy\Plugins\Shop\Contracts\{Product as ProductContract, ProductObjectType, ProductPurchasingOption};
use tiFy\Plugins\Shop\{Shop, ShopAwareTrait};
use tiFy\Wordpress\{Contracts\Query\QueryPost as QueryPostContract, Query\QueryPost};
use WP_Error;
use WP_Post;
use WP_Query;

class Product extends QueryPost implements ProductContract
{
    use ShopAwareTrait;

    /**
     * Nom de qualification du type de post ou liste de types de post associés.
     * @var string|string[]|null
     */
    protected static $postType = 'product';

    /**
     * Classe de rappel de l'Object Type.
     * @var ProductObjectType
     */
    protected $productObjectType;

    /**
     * Liste des options d'achats associées
     * @var ProductPurchasingOption[]
     */
    protected $purchasingOptions;

    /**
     * @inheritDoc
     */
    public function __construct(?WP_Post $wp_post = null)
    {
        $this->setShop(Shop::instance());

        parent::__construct($wp_post);
    }

    /**
     * @inheritDoc
     */
    public static function create($id = null, ...$args): ?QueryPostContract
    {
        if (is_numeric($id)) {
            return static::createFromId((int)$id);
        } elseif (is_string($id)) {
            return static::createFromSku($id);
        } elseif ($id instanceof WP_Post) {
            return (new static($id));
        } elseif(is_null($id) && ($instance = static::createFromGlobal())) {
            if (($postType = static::$postType) && ($postType!== 'any')) {
                return $instance->typeIn($postType) ? $instance : null;
            } else {
                return $instance;
            }
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public static function createFromSku(string $sku): ?QueryPostContract
    {
        $wpQuery = new WP_Query(static::parseQueryArgs([
            'meta_key' => '_sku',
            'meta_value' => $sku,
        ]));

        return ($wpQuery->found_posts == 1) ? new static(current($wpQuery->posts)) : null;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getCompositionProducts(): array
    {
        $products = [];

        if (
            $this->isProductType('composed') &&
            ($product_ids = $this->getMetaSingle('_composition_products'))
        ) {
            foreach ($product_ids as $product_id) {
                if ($product = $this->shop()->product($product_id)) {
                    $products[] = $product;
                }
            }
        }

        return $products;
    }

    /**
     * @inheritDoc
     */
    public function getGroupedProducts(): array
    {
        $products = [];

        if (
            $this->isProductType('grouped') &&
            ($product_ids = $this->getMetaSingle('_grouped_products'))
        ) {
            foreach ($product_ids as $product_id) {
                if ($product = $this->shop()->product($product_id)) {
                    $products[] = $product;
                }
            }
        }

        return $products;
    }

    /**
     * @inheritDoc
     */
    public function getProductObjectType(): ProductObjectType
    {
        if (!is_null($this->productObjectType)) {
            return $this->productObjectType;
        }

        return $this->productObjectType = $this->shop()->products()->getObjectType($this->getType()->getName());
    }

    /**
     * @inheritDoc
     */
    public function getProductTags(): array
    {
        $tags = wp_get_post_terms($this->getId(), 'product_tag');

        return !$tags instanceof WP_Error ? $tags : [];
    }

    /**
     * @inheritDoc
     */
    public function getProductType(): string
    {
        if (!$terms = get_the_terms($this->getId(), 'product_type')) {
            return $this->getProductObjectType()->getDefaultProductType();
        } elseif ($terms instanceof WP_Error) {
            return $this->getProductObjectType()->getDefaultProductType();
        }

        $term = reset($terms);
        if (!in_array($term->name, $this->getProductTypes())) {
            return $this->getProductObjectType()->getDefaultProductType();
        }

        return $term->name;
    }

    /**
     * @inheritDoc
     */
    public function getProductTypes(): array
    {
        return $this->getProductObjectType()->getProductTypes();
    }

    /**
     * @inheritDoc
     */
    public function getPurchasingOption(string $name): ?ProductPurchasingOption
    {
        $purchasing_options = $this->getPurchasingOptions();

        return $purchasing_options[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getPurchasingOptions(): array
    {
        if (is_null($this->purchasingOptions)) {
            $this->purchasingOptions = [];

            foreach ($this->getMetaSingle('_purchasing_options', []) as $name => $attrs) {
                if (!empty($attrs)) {
                    /** @var ProductPurchasingOption $option */
                    $option = app()->has("shop.products.purchasing_option.{$name}")
                        ? app("shop.products.purchasing_option.{$name}", [$name, $attrs, $this, $this->shop])
                        : app('shop.products.purchasing_option', [$name, $attrs, $this, $this->shop]);

                    if ($option->isActive()) {
                        $this->purchasingOptions[$name] = $option;
                    }
                }
            }
        }

        return $this->purchasingOptions;
    }

    /**
     * @inheritDoc
     */
    public function getRegularPrice(): float
    {
        return (float)$this->getMetaSingle('_regular_price', 0);
    }

    /**
     * @inheritDoc
     */
    public function getSku(): string
    {
        return (string)$this->getMetaSingle('_sku', '');
    }

    /**
     * @inheritDoc
     */
    public function getUpsellProducts(): array
    {
        $products = [];

        if ($product_ids = $this->getMetaSingle('_upsell_ids')) {
            foreach ($product_ids as $product_id) {
                if ($product = $this->shop()->product($product_id)) {
                    $products[] = $product;
                }
            }
        }

        return $products;
    }

    /**
     * @inheritDoc
     */
    public function getWeight(): float
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function isDownloadable(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isFeatured(): bool
    {
        if (!$terms = wp_get_post_terms($this->getId(), 'product_visibility', ['fields' => 'names'])) {
            return false;
        } elseif ($terms instanceof WP_Error) {
            return false;
        }

        return in_array('featured', $terms);
    }

    /**
     * @inheritDoc
     */
    public function isInStock(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isProductType(string $type): bool
    {
        return $this->getProductType() === $type;
    }

    /**
     * @inheritDoc
     */
    public function isPurchasable(): bool
    {
        return $this->isProductType('composing') ? false : ($this->getStatus()->getName() === 'publish');
    }

    /**
     * @inheritDoc
     */
    public function isVirtual(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function salePriceDisplay(): string
    {
        return $this->shop()->functions()->price()->html($this->getRegularPrice());
    }
}