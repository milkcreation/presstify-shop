<?php

namespace tiFy\Plugins\Shop\Products;

use tiFy\PostType\Query\PostQuery;
use tiFy\Plugins\Shop\{
    Contracts\ProductsInterface,
    Products\ObjectType\Categorized,
    Products\ObjectType\Uncategorized,
    Shop,
    ShopResolverTrait
};
use WP_Post;
use WP_Query;

/**
 * Class Products
 *
 * @desc Gestion des gammes de produits.
 */
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
     * @var Categorized[]|Uncategorized[]
     */
    private static $objectTypes = [];

    /**
     * Liste des types de produits permis.
     * @var string[]
     */
    private static $productTypes = [
        'simple', 'grouped', 'composed', 'composing', 'external', 'variable'
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return void
     */
    protected function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __clone() {}

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __wakeup() {}

    /**
     * Instanciation de la classe.
     *
     * @param string $alias
     * @param Shop $shop
     *
     * @return static
     */
    public static function make($alias, Shop $shop)
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

        add_action('save_post', [$this, 'save_post'], 10, 2);
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
                return self::$objectTypes[$post_type] = app(
                        'shop.products.type.uncategorized',
                        [$post_type, $attrs, $this->shop]
                    );
            else :
                return self::$objectTypes[$post_type] = app(
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
    public function getItemBy($key = 'name', $value)
    {
        $args = [
            'post_type'      => $this->getObjectName(),
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

        $wp_query = new WP_Query;
        $posts = $wp_query->query($args);
        if ($wp_query->found_posts) :
            return $this->getItem(reset($posts));
        endif;

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectName()
    {
        return $this->getObjectTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectType($object_type)
    {
        return self::$objectTypes[$object_type] ?? null;
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
            case 'composed' :
                return __('Produit composé', 'tify');
                break;
            case 'composing' :
                return __('Composition de produit', 'tify');
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
                return "<span class=\"dashicons dashicons-networking\"></span>";
                break;
            case 'composed' :
                return "<span class=\"dashicons dashicons-forms\"></span>";
                break;
            case 'composing' :
                return "<span class=\"dashicons dashicons-share-alt\"></span>";
                break;
            case 'external' :
                return "<span class=\"dashicons dashicons-migrate\"></span>";
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
        if (!$post_type = request()->post('post_type', '')) :
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

        $this->getItem($post)->save();

        return $post;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveCollection($items)
    {
        return app('shop.products.list', [$items]);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveItem(WP_Post $wp_post)
    {
        $concrete = $this->getObjectType($wp_post->post_type)->getItemController();

        return new $concrete($wp_post, $this->shop);
    }
}