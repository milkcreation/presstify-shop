<?php

/**
 * @name ProductItem
 * @desc Controleur de récupération des données d'un produit.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Products;

use tiFy\PostType\Query\PostQueryItem;
use tiFy\Plugins\Shop\Contracts\ProductItemInterface;
use tiFy\Plugins\Shop\Contracts\ProductObjectType;
use tiFy\Plugins\Shop\Contracts\ProductPurchasingOption;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class ProductItem extends PostQueryItem implements ProductItemInterface
{
    use ShopResolverTrait;

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
     * CONSTRUCTEUR
     *
     * @param \WP_Post $wp_post
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct(\WP_Post $wp_post, Shop $shop)
    {
        $this->shop = $shop;

        parent::__construct($wp_post);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getCompositionProducts()
    {
        $products = [];

        if (
            $this->isProductType('composed') &&
            ($product_ids = $this->getMetaSingle('_composition_products'))
        ) :
            foreach($product_ids as $product_id) :
                if ($product = $this->products()->getItem($product_id)) :
                    $products[] = $product;
                endif;
            endforeach;
        endif;

        return $this->products()->resolveCollection($products);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupedProducts()
    {
        $products = [];

        if (
            $this->isProductType('grouped') &&
            ($product_ids = $this->getMetaSingle('_grouped_products'))
        ) :
            foreach($product_ids as $product_id) :
                if ($product = $this->products()->getItem($product_id)) :
                    $products[] = $product;
                endif;
            endforeach;
        endif;

        return $this->products()->resolveCollection($products);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductObjectType()
    {
        if (!is_null($this->productObjectType)) :
            return $this->productObjectType;
        endif;

        return $this->productObjectType = $this->products()->getObjectType($this->getType());
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTags()
    {
        return wp_get_post_terms($this->getId(), 'product_tag');
    }

    /**
     * {@inheritdoc}
     */
    public function getProductType()
    {
        if (!$terms = get_the_terms($this->getId(), 'product_type')) :
            return $this->getProductObjectType()->getDefaultProductType();
        elseif (is_wp_error($terms)) :
            return $this->getProductObjectType()->getDefaultProductType();
        endif;

        $term = reset($terms);
        if (!in_array($term->name, $this->getProductTypes())) :
            return $this->getProductObjectType()->getDefaultProductType();
        endif;

        return $term->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTypes()
    {
        return $this->getProductObjectType()->getProductTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function getPurchasingOption($name)
    {
        $purchasing_options = $this->getPurchasingOptions();

        return $purchasing_options[$name] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPurchasingOptions()
    {
        if (is_null($this->purchasingOptions)) :
            $this->purchasingOptions = [];

            foreach($this->getMetaSingle('_purchasing_options', []) as $name => $attrs) :
                /** @var ProductPurchasingOption $option */
                $option = app()->bound("shop.products.purchasing_option.{$name}")
                    ? app("shop.products.purchasing_option.{$name}", [$name, $attrs, $this, $this->shop])
                    : app('shop.products.purchasing_option', [$name, $attrs, $this, $this->shop]);

                if ($option->isActive()) :
                    $this->purchasingOptions[$name] = $option;
                endif;
            endforeach;
        endif;

        return $this->purchasingOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegularPrice()
    {
        return $this->getMetaSingle('_regular_price', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->getMetaSingle('_sku', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getUpsellProducts()
    {
        $products = [];

        if ($product_ids = $this->getMetaSingle('_upsell_ids')) :
            foreach ($product_ids as $product_id) :
                if ($product = $this->products()->getItemBy('sku', $product_id)) :
                    $products[] = $product;
                endif;
            endforeach;
        endif;

        return $this->products()->resolveCollection($products);
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isDownloadable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isFeatured()
    {
        if (!$terms = wp_get_post_terms($this->getId(), 'product_visibility', ['fields' => 'names'])) :
            return false;
        elseif (is_wp_error($terms)) :
            return false;
        endif;

        return in_array('featured', $terms);
    }

    /**
     * {@inheritdoc}
     */
    public function isInStock()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isProductType($type)
    {
        return $this->getProductType() === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function isPurchasable()
    {
        return ($this->getStatus() === 'publish');
    }

    /**
     * {@inheritdoc}
     */
    public function isVirtual()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function salePriceDisplay()
    {
        return $this->functions()->price()->html($this->getRegularPrice());
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        // -----------------------------------------------------------
        // TYPE DE PRODUIT
        $product_type = request()->post('product-type', $this->getProductObjectType()->getDefaultProductType());
        wp_set_post_terms($this->getId(), $product_type, 'product_type');

        // -----------------------------------------------------------
        // VISIBILITE PRODUIT
        $visibility = [];

        // Mise en avant
        $featured = request()->post('_featured', 'off');
        if ($featured === 'on') :
            array_push($visibility, 'featured');
        endif;

        wp_set_post_terms($this->getId(), $visibility, 'product_visibility');
    }
}