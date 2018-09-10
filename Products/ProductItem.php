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
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class ProductItem extends PostQueryItem implements ProductItemInterface
{
    use ShopResolverTrait;

    /**
     * Classe de rappel de l'Object Type.
     * @var ObjectTypes\Categorized|ObjectTypes\Uncategorized
     */
    private $productObjectType;

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
    public function getGroupedProducts()
    {
        return \get_post_meta($this->getId(), '_grouped_products', true) ? : [];
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
        return \wp_get_post_terms($this->getId(), 'product_tag');
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
    public function getProductType()
    {
        if (!$terms = get_the_terms($this->getId(), 'product_type')) :
            return 'simple';
        elseif (is_wp_error($terms)) :
            return 'simple';
        endif;

        $term = reset($terms);
        if (!in_array($term->name, $this->getProductTypes())) :
            return 'simple';
        endif;

        return $term->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPurchasingOption($name)
    {
        return app('shop.products.purchasing_option', [$name, $this, $this->shop]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRegularPrice()
    {
        return get_post_meta($this->getId(), '_regular_price', true);
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return get_post_meta($this->getId(), '_sku', true);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpsellProducts()
    {
        return \get_post_meta($this->getId(), '_upsell_ids', true) ? : [];
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
        if (!$terms = \wp_get_post_terms($this->getId(), 'product_visibility', ['fields' => 'names'])) :
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
        $product_type = request()->getProperty('POST')->get('product-type', 'simple');
        \wp_set_post_terms($this->getId(), $product_type, 'product_type');

        // -----------------------------------------------------------
        // VISIBILITE PRODUIT
        $visibility = [];

        // Mise en avant
        $featured = request()->getProperty('POST')->get('_featured', 'off');
        if ($featured === 'on') :
            array_push($visibility, 'featured');
        endif;

        \wp_set_post_terms($this->getId(), $visibility, 'product_visibility');
    }
}