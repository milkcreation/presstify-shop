<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Admin\Edit;

use tiFy\Plugins\Shop\Contracts\{
    ProductObjectType,
    ShopInterface as Shop
};
use tiFy\Plugins\Shop\Products\ObjectType\{Categorized, Uncategorized};
use tiFy\Plugins\Shop\ShopAwareTrait;
use WP_Post;
use WP_Screen;

class Edit
{
    use ShopAwareTrait;

    /**
     * Nom de qualification du type de post associé.
     * @var string
     */
    private $objectName = '';

    /**
     * Instance du type de produit.
     * @var Categorized|Uncategorized
     */
    private $objectType;

    /**
     * CONSTRUCTEUR
     *
     * @param ProductObjectType $object_type Instance du type de produit.
     * @param Shop $shop Attributs de configuration.
     *
     * @return void
     */
    public function __construct(ProductObjectType $object_type, Shop $shop)
    {
        $this->setShop($shop);

        $this->objectType = $object_type;
        $this->objectName = $this->objectType->getName();

        add_action('current_screen', function (WP_Screen $wp_screen) {
            if ($wp_screen->id !== (string)$this->objectName) {
                return;
            }

            add_action('admin_enqueue_scripts', function() {
                field('select-js')->enqueue();
                field('toggle-switch')->enqueue();

                wp_enqueue_script(
                    'ShopAdminProductEdit',
                    $this->shop()->resourcesUrl() . '/assets/js/admin-edit.js',
                    ['jquery'],
                    171219,
                    true
                );

                wp_enqueue_style(
                    'ShopAdminProductEdit',
                    $this->shop()->resourcesUrl() . '/assets/css/admin-edit.css',
                    [],
                    171219
                );
            });
        });

        /** @todo COMPATIBILITE tiFY 2.0
        // @var MetaboxManager $metabox
        $metabox = app('metabox');

        $metabox->tab([
            'title' => function (WP_Post $post) {
                return $this->panelHeader($post);
            },
        ], "{$this->objectType}@post_type");
        */

        // Définition des onglets de saisie par défaut
        $default_tabs = [
            'general'    => [
                'title'    => __('Général', 'tify'),
                'content'  => [$this, 'generalPanel'],
                'position' => 1,
            ],
            'inventory'  => [
                'title'    => __('Inventaire', 'tify'),
                'content'  => [$this, 'inventoryPanel'],
                'position' => 2,
            ],
            'shipping'   => [
                'title'    => __('Expédition', 'tify'),
                'content'  => [$this, 'shippingPanel'],
                'position' => 3,
            ],
            'linked'     => [
                'title'    => __('Produits liés', 'tify'),
                'content'  => [$this, 'linkedPanel'],
                'position' => 4,
            ],
            'attributes' => [
                'title'    => __('Attributs', 'tify'),
                'content'  => [$this, 'attributesPanel'],
                'position' => 5,
            ],
            'variations' => [
                'title'    => __('Variations', 'tify'),
                'content'  => [$this, 'variationsPanel'],
                'position' => 6,
            ],
            'advanced'   => [
                'title'    => __('Avancé', 'tify'),
                'content'  => [$this, 'advancedPanel'],
                'position' => 7,
            ],
        ];

        // Récupération des onglets personalisés
        $custom_tabs = $this->objectType->get('tabs', []);

        foreach ($default_tabs as $id => $default_tab) {
            if (!isset($custom_tabs[$id])) :
                $custom_tabs[$id] = $default_tab;
            elseif ($custom_tabs[$id] !== false) :
                $custom_tabs[$id] = array_merge($default_tab, (array)$custom_tabs[$id]);
            else :
                unset($custom_tabs[$id]);
            endif;
        }

        /** @todo COMPATIBILITE tiFY 2.0
        foreach ($custom_tabs as $id => $attrs) {

            $metabox->add(
                "ShopProduct-{$id}--{$this->objectType}",
                "{$this->objectType}@post_type",
                $attrs
            );
        }*/
    }

    /**
     * Titre du panneau de saisie
     *
     * @param WP_Post $post
     *
     * @return string
     */
    public function panelHeader($post)
    {
        $product = $this->shop()->products()->getItem($post);

        $product_type_selector = '';
        if ($product_types = $product->getProductTypes()) :
            $product_type_options = [];
            foreach ($product_types as $product_type) :
                $product_type_options[$product_type] = $this->shop()
                    ->products()
                    ->getProductTypeDisplayName($product_type);
            endforeach;

            $product_type_selector .= '<b> — </b>';
            $product_type_selector .= field(
                'select',
                [
                    'name'     => 'product-type',
                    'value'    => $product->getProductType(),
                    'choices'  => $product_type_options
                ]
            );
        else :
            $product_type_selector .= field(
                'hidden',
                [
                    'name'    => 'product-type',
                    'choices' => 'simple'
                ]
            );
        endif;

        return '<b>' . __('Données produit', 'tify') . '</b>' . $product_type_selector;
    }

    /**
     * Saisie des options générales
     *
     * @param WP_Post $post Object Post Wordpress
     *
     * @return string
     */
    public function generalPanel($post)
    {
        $product = $this->shop()->products()->getItem($post);

        return $this->shop()->viewer('admin/edit/general', compact('post', 'product'));
    }

    /**
     * Saisie des données d'inventaire
     *
     * @param WP_Post $post Object Post Wordpress
     *
     * @return string
     */
    public function inventoryPanel($post)
    {
        $product = $this->shop()->products()->getItem($post);

        return $this->shop()->viewer('admin/edit/inventory', compact('post', 'product'));
    }

    /**
     * Saisie des données de livraison
     *
     * @param WP_Post $post Object Post Wordpress
     *
     * @return string
     */
    public function shippingPanel($post)
    {
        $product = $this->shop()->products()->getItem($post);

        return $this->shop()->viewer('admin/edit/shipping', compact('post', 'product'));
    }

    /**
     * Saisie des données de produits liés
     *
     * @param WP_Post $post Object Post Wordpress
     *
     * @return string
     */
    public function linkedPanel($post)
    {
        $product = $this->shop()->products()->getItem($post);

        return $this->shop()->viewer('admin/edit/linked', compact('post', 'product'));
    }

    /**
     * Saisie des données d'attributs
     *
     * @param WP_Post $post Object Post Wordpress
     *
     * @return string
     */
    public function attributesPanel($post)
    {
        $product = $this->shop()->products()->getItem($post);

        return $this->shop()->viewer('admin/edit/attributes', compact('post', 'product'));
    }

    /**
     * Saisie des données de variations
     *
     * @param WP_Post $post Object Post Wordpress
     *
     * @return string
     */
    public function variationsPanel($post)
    {
        $product = $this->shop()->products()->getItem($post);

        return $this->shop()->viewer('admin/edit/variations', compact('post', 'product'));
    }

    /**
     * Saisie des données de advanced
     *
     * @param WP_Post $post Object Post Wordpress
     *
     * @return string
     */
    public function advancedPanel($post)
    {
        $product = $this->shop()->products()->getItem($post);

        return $this->shop()->viewer('admin/edit/advanced', compact('post', 'product'));
    }
}