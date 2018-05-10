<?php

namespace tiFy\Plugins\Shop\Products\ObjectTypes;

use Illuminate\Support\Arr;
use tiFy\Apps\AppController;
use tiFy\PostType\PostType;
use tiFy\Metadata\Post as MetadataPost;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\Products\ProductItem;

abstract class Factory extends AppController
{
    /**
     * Nom de qualification du type de post.
     * @var string
     */
    protected $name = '';

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Liste des attributs de configuration
     * @var array
     */
    protected $attributes = [];

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     * @param string $post_type Identifiant de qualification du type de post
     * @param array $attrs Attributs de configuration
     *
     * @return void
     */
    public function __construct(Shop $shop, $name, $attrs = [])
    {
        parent::__construct();

        $this->shop = $shop;
        $this->name = $name;
        $this->attributes = $this->parse($attrs);
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('tify_post_type_register');
        $this->_registerMetas();
    }

    /**
     * Déclaration des types de posts personnalisés des gammes de produits.
     *
     * @param PostType $post_type Classe de rappel de traitement des types de post.
     *
     * @return void
     */
    final public function tify_post_type_register($post_type)
    {
        $post_type->register(
            $this->getName(),
            $this->all()
        );
    }

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Traitement de arguments de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return array
     */
    protected function parse($attrs = [])
    {
        if (empty($attrs['label'])) :
            $attrs['label'] = _x(sprintf('Produits de la gamme %s', $this->getName()), 'post type general name', 'tify');
        endif;

        if (empty($attrs['plural'])) :
            $attrs['plural'] = _x(sprintf('Produits de la gamme %s', $this->getName()), 'post type plural name', 'tify');
        endif;

        if (empty($attrs['singular'])) :
            $attrs['singular'] = _x(sprintf('Produit de la gamme %s', $this->getName()), 'post type singular name', 'tify');
        endif;

        if (empty($attrs['menu_icon'])) :
            $attrs['menu_icon'] = 'dashicons-products';
        endif;

        if (isset($attrs['tag']) &&  ($attrs['tag'] === true)) :
            $attrs['taxonomies'] = 'product_tag';
        endif;

        return $attrs;
    }

    /**
     * Récupération de la liste des attributs de configuration.
     *
     * @return array
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'index de qualification de l'attribut.
     * @param mixed $default Valeur de retoru par defaut.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
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
            '_grouped_products'
        ];

        foreach ($single_meta_keys as $single_meta_key) :
            $this->appServiceGet(MetadataPost::class)->register($this->getName(), $single_meta_key, true);
        endforeach;
    }

    /**
     * Récupération du controleur de récupération de données d'un produit
     *
     * @return string
     */
    final public function getItemController()
    {
        if (!$controller = $this->get('item_controller')) :
            return ProductItem::class;
        endif;

        return $controller;
    }

    /**
     * Vérifie s'il s'agit d'une gamme de produit unique
     *
     * @return bool
     */
    final public function hasCat()
    {
        return $this->get('category', false);
    }

    /**
     * Récupération de la liste des types de produit
     *
     * @return array
     */
    final public function getProductTypes()
    {
        $allowed_types = $this->shop->products()->getProductTypes();

        if (!$product_types = $this->get('product_types', [])) :
            return $allowed_types;
        else :
            return array_intersect($product_types, $allowed_types);
        endif;
    }

    /**
     * Récupération de l'identifiant de qualification du type de post de définition du produit
     *
     * @return string
     */
    final public function __toString()
    {
        return $this->getName();
    }
}