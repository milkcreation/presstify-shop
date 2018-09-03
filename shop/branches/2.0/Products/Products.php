<?php

/**
 * @name Products
 * @desc Gestion des gammes de produits.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Products;

use LogicException;
use tiFy\PostType\Query\PostQuery;
use tiFy\Plugins\Shop\Contracts\ProductsInterface;
use tiFy\Plugins\Shop\Products\ObjectType\Categorized;
use tiFy\Plugins\Shop\Products\ObjectType\Uncategorized;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class Products extends PostQuery implements ProductsInterface
{
    use ShopResolverTrait;

    /**
     * Instance de la classe.
     * @var static
     */
    protected static $instance;

    /**
     * Liste des classes de rappel des gammes de produits déclarées.
     * @var ObjectTypes\Categorized[]|ObjectTypes\Uncategorized[]
     */
    private static $objectTypes = [];

    /**
     * Liste des types de produits permis.
     * @var string[]
     */
    private static $productTypes = [
        'simple', 'grouped', 'external', 'variable'
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return null
     */
    protected function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return null
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return null
     */
    private function __wakeup()
    {

    }

    /**
     * Instanciation de la classe
     *
     * @param Shop $shop
     *
     * @return AddressesInterface
     */
    public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new static($shop);
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->_registerObjectTypes();

        $this->app()->appAddAction('save_post', [$this, 'save_post'], 10, 2);
    }

    /**
     * Déclaration des gammes de produit
     *
     * @return null|Categorized|Uncategorized
     */
    private function _registerObjectTypes()
    {
        foreach ($this->config('products', []) as $post_type => $attrs) :
            if (empty($attrs['category'])) :
                return self::$objectTypes[$post_type] = $this->app(
                        'shop.products.type.uncategorized',
                        [$post_type, $attrs, $this->shop]
                    );
            else :
                return self::$objectTypes[$post_type] = $this->app(
                        'shop.products.type.categorized',
                        [$post_type, $attrs, $this->shop]
                    );
            endif;
        endforeach;

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection($query_args = [])
    {
        if (!$query_args['post_type'] = $this->getObjectTypes()) :
            return [];
        endif;

        if (!isset($query_args['posts_per_page'])) :
            $query_args['posts_per_page'] = -1;
        endif;

        $wp_query = new \WP_Query;
        $posts = $wp_query->query($query_args);

        if ($posts) :
            $items =  array_map([$this, 'get'], $posts);
        else :
            $items = [];
        endif;

        $controller = $this->getListController();

        return new $controller($items);
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionController()
    {
        return $this->app('shop.products.list');
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($product = null)
    {
        if (is_string($product)) :
            return $this->getItemBy(null, $product);
        elseif (!$product) :
            $post = get_the_ID();
        else :
            $post = $product;
        endif;

        if (!$post = \get_post($product)) :
            return null;
        endif;

        if (!$post instanceof \WP_Post) :
            return null;
        endif;

        if (!in_array($post->post_type, (array) $this->getObjectTypes())) :
            return null;
        endif;

        $alias = 'tify.query.post.' . $post->ID;
        if (! app()->has($alias)) :
            $controller = $this->getObjectType($post->post_type)->getItemController();
            app()->singleton($alias, new $controller($post, $this->shop));
        endif;

        return $this->app($alias);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemBy($key = 'name', $value)
    {
        $args = [
            'post_type'      => $this->getObjectTypes(),
            'posts_per_page' => 1
        ];

        switch ($key) :
            default :
            case 'post_name' :
            case 'name' :
                $args['name'] = $value;
                break;
            case 'sku' :
                $args['meta_query'] = [
                    [
                        'key'   => '_sku',
                        'value' => $value
                    ]
                ];
                break;
        endswitch;

        $wp_query = new \WP_Query;
        $posts = $wp_query->query($args);
        if ($wp_query->found_posts) :
            return $this->getItem(reset($posts));
        endif;

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectType($object_type)
    {
        if (isset(self::$objectTypes[$object_type])) :
            return self::$objectTypes[$object_type];
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectTypeList()
    {
        return self::$objectTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectTypes()
    {
        return array_keys(self::$objectTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTypeDisplayName($product_type)
    {
        switch($product_type) :
            case 'simple' :
                return __('Produit simple', 'tify');
                break;
            case 'grouped' :
                return __('Produit groupés', 'tify');
                break;
            case 'external' :
                return __('Produit externe/affiliation', 'tify');
                break;
            case 'variable' :
                return __('Produit variable', 'tify');
                break;
            default :
                return '';
                break;
        endswitch;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTypeIcon($product_type)
    {
        switch($product_type) :
            case 'simple' :
                return "<span class=\"dashicons dashicons-products\"></span>";
                break;
            case 'grouped' :
                return "<span class=\"dashicons dashicons-forms\"></span>";
                break;
            case 'external' :
                return "<span class=\"dashicons dashicons-share-alt\"></span>";
                break;
            case 'variable' :
                return "<span class=\"dashicons dashicons-chart-area\"></span>";
                break;
            default :
                return '';
                break;
        endswitch;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTypes()
    {
        return self::$productTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function save_post($post_id, $post)
    {
        // Bypass - S'il s'agit d'une routine de sauvegarde automatique.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) :
            return null;
        endif;

        // Bypass - Si le script est executé via Ajax.
        if (defined('DOING_AJAX') && DOING_AJAX) :
            return null;
        endif;

        // Bypass - Si l'argument de requête renseignant l'indication de type de post est manquant
        if (!$post_type = $this->app()->appRequest('POST')->get('post_type', '')) :
            return null;
        endif;

        // Bypass - Si l'utilisateur courant n'est pas habilité  à modifié le contenu.
        if (('page' === $post_type) && !current_user_can('edit_page', $post_id)) :
            return null;
        endif;
        if (('page' !== $post_type) && !current_user_can('edit_post', $post_id)) :
            return null;
        endif;

        // Bypass - Si la vérification de l'existance du post est en échec.
        if ((!$post = get_post($post_id))) :
            return null;
        endif;

        // Bypass - Si le type de post définit dans la requête est différent du type de post du contenu a éditer
        if ($post_type !== $post->post_type) :
            return null;
        endif;

        // Bypass - Le type de post doit être faire partie d'une gamme de produit déclaré
        if (!in_array($post_type, array_keys(self::$objectTypes))) :
            return null;
        endif;

        $this->get($post)->save();

        return $post;
    }
}