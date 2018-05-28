<?php

/**
 * @name Products
 * @desc Gestion des gammes de produits
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Products
 * @version 1.1
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Products;

use LogicException;
use tiFy\Query\Controller\AbstractPostQuery;
use tiFy\Plugins\Shop\Shop;

class Products extends AbstractPostQuery implements ProductsInterface
{
    /**
     * Instance de la classe
     * @var Products
     */
    private static $instance;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Liste des classes de rappel des gammes de produits déclarées
     * @var ObjectTypes\Categorized[]|ObjectTypes\Uncategorized[]
     */
    private static $ObjectTypes = [];

    /**
     * Liste des type de produits permis
     * @var string[]
     */
    private static $ProductTypes = [
        'simple', 'grouped', 'external', 'variable'
    ];

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     *
     * @return void
     */
    protected function __construct(Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Déclaration des gammes de produit
        $this->registerObjectTypes();

        // Déclaration des événements
        $this->appAddAction('save_post', 'save_post', 10, 2);
    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __wakeup()
    {

    }

    /**
     * Instanciation de la classe
     *
     * @param Shop $shop
     *
     * @return Products
     */
    final public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        self::$instance = new static($shop);

        if(! self::$instance instanceof Products) :
            throw new LogicException(
                sprintf(
                    __('Le controleur de surcharge doit hériter de %s', 'tify'),
                    Products::class
                ),
                500
            );
        endif;

        return self::$instance;
    }

    /**
     * Récupération du controleur de données d'une liste d'éléments
     *
     * @return string
     */
    final public function getListController()
    {
        return $this->shop->provider()->getMapController('products.list');
    }

    /**
     * Récupération des données d'un client existant
     *
     * @param null|int|string|\WP_Post $product Identification du produit. Produit de la page courante|ID WP|post_name WP|Objet Post WP|Objet produit courant
     *
     * @return null|object|ProductItemInterface
     */
    public function get($product = null)
    {
        if (is_string($product)) :
            return $this->getBy(null, $product);
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

        $name = 'tify.query.post.' . $post->ID;
        if (! $this->appServiceHas($name)) :
            $controller = $this->getObjectType($post->post_type)->getItemController();
            $this->appServiceAdd($name, new $controller($this->shop, $post));
        endif;

        return $this->appServiceGet($name);
    }

    /**
     * Instanciation selon un attribut particulier
     *
     * @param string $key Identifiant de qualification de l'attribut. défaut name.
     * @param string $value Valeur de l'attribut
     *
     * @return null|object|ProductItemInterface
     */
    public function getBy($key = 'name', $value)
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
            return $this->get(reset($posts));
        endif;

        return null;
    }

    /**
     * Récupération des données d'une liste d'élément selon des critères de requête
     *
     * @param array $query_args Liste des arguments de requête
     *
     * @return array|ProductItemInterface[]
     */
    public function getList($query_args = [])
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
     * Déclaration des gammes de produit
     *
     * @return null|ObjectTypes\Categorized|ObjectTypes\Uncategorized
     */
    private function registerObjectTypes()
    {
        foreach ($this->shop->appConfig('products', []) as $post_type => $attrs) :
            if (empty($attrs['category'])) :
                return self::$ObjectTypes[$post_type] = new ObjectTypes\Uncategorized($this->shop, $post_type, $attrs);
            else :
                return self::$ObjectTypes[$post_type] = new ObjectTypes\Categorized($this->shop, $post_type, $attrs);
            endif;
        endforeach;

        return null;
    }

    /**
     * Enregistrement d'un post
     *
     * @param int $post_id Identifiant de qualification du post
     * @param \WP_Post $post Objet Post Wordpress
     *
     * @return array|null|\WP_Post
     */
    final public function save_post($post_id, $post)
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
        if (!$post_type = $this->appRequest('POST')->get('post_type', '')) :
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
        if (!in_array($post_type, array_keys(self::$ObjectTypes))) :
            return null;
        endif;

        $this->get($post)->save();

        return $post;
    }

    /**
     * Récupération de la liste des classe de rappel des types d'objet
     *
     * @return ObjectTypes\Categorized[]|ObjectTypes\Uncategorized[]
     */
    final public function getObjectTypeList()
    {
        return self::$ObjectTypes;
    }

    /**
     * Récupération d'une classe de rappel des types d'objet
     *
     * @param string $object_type Identifiant de qualification du type d'object (custom_post_type)
     *
     * @return ObjectTypes\Categorized|ObjectTypes\Uncategorized
     */
    final public function getObjectType($object_type)
    {
        if (isset(self::$ObjectTypes[$object_type])) :
            return self::$ObjectTypes[$object_type];
        endif;
    }

    /**
     * Récupération de la liste des identifiant de qualification des gamme de produits déclarés
     *
     * @return string[]
     */
    final public function getObjectTypes()
    {
        return array_keys(self::$ObjectTypes);
    }

    /**
     * Récupération de la liste des types de produit
     *
     * @return array
     */
    final public function getProductTypes()
    {
        return self::$ProductTypes;
    }

    /**
     * Récupération du nom d'affichage d'un type de produit
     *
     * @param string $product_type Identifiant de qualification d'un type de produit permis. simple|grouped|external|variable.
     *
     * @return string
     */
    final public function getProductTypeDisplayName($product_type)
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
     * Récupération de l'icône représentative d'un type de produit
     *
     * @param string $product_type Identifiant de qualification d'un type de produit permis. simple|grouped|external|variable.
     *
     * @return string
     */
    final public function getProductTypeIcon($product_type)
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
}