<?php

namespace tiFy\Plugins\Shop\Products\ObjectType;

use Illuminate\Support\Arr;
use tiFy\PostType\PostType;
use tiFy\Metadata\Post as MetadataPost;
use tiFy\Plugins\Shop\Contracts\ProductObjectType;
use tiFy\Plugins\Shop\Products\ProductItem;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

abstract class AbstractObjectType implements ProductObjectType
{
    use ShopResolverTrait;

    /**
     * Nom de qualification du type de post.
     * @var string
     */
    protected $name = '';

    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [];

    /**
     * CONSTRUCTEUR
     *
     * @param string $post_type Identifiant de qualification du type de post.
     * @param array $attrs Attributs de configuration.
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], Shop $shop)
    {
        $this->shop = $shop;
        $this->name = $name;

        $this->parse($attrs);

        $this->boot();
    }

    /**
     * Déclaration des métadonnées relatives aux produits.
     *
     * @return void
     */
    private function _registerMetas()
    {
        $single_meta_keys = [
            '_sku', '_regular_price', '_sale_price', '_sale_price_dates_from', '_sale_price_dates_to',
            '_tax_status', '_tax_class', '_manage_stock', '_backorders', '_sold_individually',
            '_weight', '_length', '_width', '_height', '_upsell_ids', '_crosssell_ids',
            '_purchase_note', '_default_attributes', '_virtual', '_downloadable', '_product_image_gallery',
            '_download_limit', '_download_expiry', '_stock', '_stock_status', '_product_version', '_product_attributes',
            '_grouped_products',
        ];

        /** @var MetadataPost $postMetaController */
        $postMetaController = app(MetadataPost::class);

        foreach ($single_meta_keys as $single_meta_key) :
            $postMetaController->register($this->getName(), $single_meta_key, true);
        endforeach;
    }

    /**
     * Récupération de l'identifiant de qualification du type de post de définition du produit.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->_registerMetas();

        add_action(
            'tify_post_type_register',
            function ($postTypeController) {
                /** @var  PostType $postTypeController */
                $postTypeController->register(
                    $this->getName(),
                    $this->all()
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'label'     => _x(sprintf('Produits de la gamme %s', $this->getName()), 'post type general name', 'tify'),
            'plural'    => _x(sprintf('Produits de la gamme %s', $this->getName()), 'post type plural name', 'tify'),
            'singular'  => _x(sprintf('Produit de la gamme %s', $this->getName()), 'post type singular name', 'tify'),
            'menu_icon' => 'dashicons-products',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return Arr::has($this->attributes, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemController()
    {
        if (!$controller = $this->get('item_controller')) :
            return ProductItem::class;
        endif;

        return $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTypes()
    {
        $allowed_types = $this->shop->products()->getProductTypes();

        if (!$product_types = $this->get('product_types', [])) :
            return $allowed_types;
        else :
            return array_intersect($product_types, $allowed_types);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCat()
    {
        return $this->get('category', false);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->attributes = array_merge(
            $this->defaults(),
            $attrs
        );

        if (($tag = $this->get('tag', false)) && $tag === true) :
            $this->set('taxonomies', 'product_tag');
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);

        return $this;
    }
}