<?php

namespace tiFy\Plugins\Shop\Products\ObjectTypes;

use Illuminate\Support\Arr;
use tiFy\App\FactoryConstructor;
use tiFy\Core\CustomType\CustomType;
use tiFy\Core\Meta\Post as MetaPost;
use tiFy\Plugins\Shop\Shop;

abstract class Factory extends FactoryConstructor
{
    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     * @param string $post_type Identifiant de qualification du type de post
     * @param array $attrs Attributs de configuration
     *
     * @return void
     */
    public function __construct(Shop $shop, $post_type, $attrs = [])
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        parent::__construct($post_type, $attrs);

        // Déclaration des événements de déclenchement
        $this->appAddAction('tify_custom_post_type_register');

        // Déclaration des metadonnées d'enregistrement
        $this->_registerMetas();
    }

    /**
     * Déclaration des types de posts personnalisés des gammes de produits
     *
     * @return void
     */
    final public function tify_custom_post_type_register()
    {
        CustomType::registerPostType(
            $this->getId(),
            $this->getAttrList()
        );
        $tag = Arr::get($this->getAttrList(), 'tag', true);

        if (($tag === true) || ($tag === 'product_tag')) :
            $this->appAddAction(
                'tify_custom_post_register_taxonomy_for_object_type',
                function() {
                    \register_taxonomy_for_object_type('product_tag', $this->getId());
                }
            );
        endif;
    }

    /**
     * Traitement de arguments de configuration
     *
     * @param array $attrs
     *
     * @return array
     */
    protected function parseAttrs($attrs = [])
    {
        if (empty($attrs['label'])) :
            $attrs['label'] = _x(sprintf('Produits de la gamme %s', $this->getId()), 'post type general name', 'tify');
        endif;

        if (empty($attrs['plural'])) :
            $attrs['plural'] = _x(sprintf('Produits de la gamme %s', $this->getId()), 'post type plural name', 'tify');
        endif;

        if (empty($attrs['singular'])) :
            $attrs['singular'] = _x(sprintf('Produit de la gamme %s', $this->getId()), 'post type singular name', 'tify');
        endif;

        if (empty($attrs['menu_icon'])) :
            $attrs['menu_icon'] = 'dashicons-products';
        endif;

        return $attrs;
    }

    /**
     * Déclaration des métadonnées relatives aux produits
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
            MetaPost::register($this->getId(), $single_meta_key, true);
        endforeach;
    }

    /**
     * Récupération du controleur de récupération de données d'un produit
     *
     * @return string
     */
    final public function getItemController()
    {
        if (!$controller = $this->getAttr('item_controller')) :
            return 'tiFy\Plugins\Shop\Products\ProductItem';
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
        return $this->getAttr('category', false);
    }

    /**
     * Récupération de la liste des types de produit
     *
     * @return array
     */
    final public function getProductTypes()
    {
        $allowed_types = $this->shop->products()->getProductTypes();

        if (!$this->issetAttr('product_types')) :
            return $allowed_types;
        else :
            return array_intersect($this->getAttr('product_types', []), $allowed_types);
        endif;
    }

    /**
     * Récupération de l'identifiant de qualification du type de post de définition du produit
     *
     * @return string
     */
    final public function __toString()
    {
        return $this->getId();
    }
}