<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Products;

use tiFy\Plugins\Shop\Contracts\{Product as ProductContract, ProductObjectType, ProductPurchasingOption};
use tiFy\Plugins\Shop\{Shop, ShopAwareTrait};
use tiFy\Wordpress\{Contracts\Query\QueryPost as QueryPostContract, Query\QueryPost};
use WP_Post;
use WP_Query;

class Product extends QueryPost implements ProductContract
{
    use ShopAwareTrait;

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
            return static::createFromId($id);
        } elseif (is_string($id)) {
            return static::createFromSku($id);
        } elseif ($id instanceof WP_Post) {
            return (new static($id));
        } elseif(is_null($id)) {
            return static::createFromGlobal();
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public static function createFromSku(string $sku): ?QueryPostContract
    {
        $wpQuery = new WP_Query([
            'post_type' => static::$postType ? : 'any',
            'meta_key' => '_sku',
            'meta_value' => $sku
        ]);

        return ($wpQuery->found_posts === 1) ? new static(current($wpQuery->posts)) : null;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getCompositionProducts()
    {
        $products = [];

        if (
            $this->isProductType('composed') &&
            ($product_ids = $this->getMetaSingle('_composition_products'))
        ) {
            foreach ($product_ids as $product_id) {
                if ($product = $this->shop()->products()->get($product_id)) {
                    $products[] = $product;
                }
            }
        }

        return $this->shop()->products()->resolveCollection($products);
    }

    /**
     * @inheritDoc
     */
    public function getGroupedProducts()
    {
        $products = [];

        if (
            $this->isProductType('grouped') &&
            ($product_ids = $this->getMetaSingle('_grouped_products'))
        ) {
            foreach ($product_ids as $product_id) {
                if ($product = $this->shop()->products()->getItem($product_id)) {
                    $products[] = $product;
                }
            }
        }

        return $this->shop()->products()->resolveCollection($products);
    }

    /**
     * @inheritDoc
     */
    public function getProductObjectType()
    {
        if (!is_null($this->productObjectType)) {
            return $this->productObjectType;
        }

        return $this->productObjectType = $this->shop()->products()->getObjectType($this->getType()->getName());
    }

    /**
     * @inheritDoc
     */
    public function getProductTags()
    {
        return wp_get_post_terms($this->getId(), 'product_tag');
    }

    /**
     * @inheritDoc
     */
    public function getProductType()
    {
        if (!$terms = get_the_terms($this->getId(), 'product_type')) {
            return $this->getProductObjectType()->getDefaultProductType();
        } elseif (is_wp_error($terms)) {
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
    public function getProductTypes()
    {
        return $this->getProductObjectType()->getProductTypes();
    }

    /**
     * @inheritDoc
     */
    public function getPurchasingOption($name)
    {
        $purchasing_options = $this->getPurchasingOptions();

        return $purchasing_options[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getPurchasingOptions()
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
    public function getRegularPrice()
    {
        return $this->getMetaSingle('_regular_price', 0);
    }

    /**
     * @inheritDoc
     */
    public function getSku()
    {
        return $this->getMetaSingle('_sku', '');
    }

    /**
     * @inheritDoc
     */
    public function getUpsellProducts()
    {
        $products = [];

        if ($product_ids = $this->getMetaSingle('_upsell_ids')) {
            foreach ($product_ids as $product_id) {
                if ($product = $this->shop()->products()->getItemBy('sku', $product_id)) {
                    $products[] = $product;
                }
            }
        }

        return $this->shop()->products()->resolveCollection($products);
    }

    /**
     * @inheritDoc
     */
    public function getWeight()
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function isDownloadable()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isFeatured()
    {
        if (!$terms = wp_get_post_terms($this->getId(), 'product_visibility', ['fields' => 'names'])) {
            return false;
        } elseif (is_wp_error($terms)) {
            return false;
        }

        return in_array('featured', $terms);
    }

    /**
     * @inheritDoc
     */
    public function isInStock()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isProductType($type)
    {
        return $this->getProductType() === $type;
    }

    /**
     * @inheritDoc
     */
    public function isPurchasable()
    {
        return $this->isProductType('composing') ? false : $this->getStatus() === 'publish';
    }

    /**
     * @inheritDoc
     */
    public function isVirtual()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function salePriceDisplay()
    {
        return $this->shop()->functions()->price()->html($this->getRegularPrice());
    }
}