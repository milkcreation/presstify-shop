<?php
namespace tiFy\Plugins\Shop\Admin\Edit;

use tiFy\Apps\AppController;
use tiFy\Field\Field;
use tiFy\Taboox\Taboox;
use tiFy\Plugins\Shop\Shop;

class Edit extends AppController
{
    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;
    
    /**
     * Classe de rappel d'un produit
     * @var \tiFy\Plugins\Shop\Products\ObjectTypes\Categorized|\tiFy\Plugins\Shop\Products\ObjectTypes\Uncategorized
     */
    private $objectType;

    /**
     * CONSTRUCTEUR
     *
     * @param string $id Identifiant de qualification du type de post du produit
     * @param array $attrs Attributs de configuration
     *
     * @return void
     */
    public function __construct(Shop $shop, \tiFy\Plugins\Shop\Products\ObjectTypes\Factory $ObjectType)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        $this->objectType = $ObjectType;

        // Déclaration des événements de déclenchement
        $this->appAddAction('tify_taboox_register_box');
        $this->appAddAction('tify_taboox_register_node');
        $this->appAddAction('current_screen');
    }

    /**
     * Déclaration de la liste des organes de saisie
     *
     * @return void
     */
    final  public function tify_taboox_register_box()
    {
        Taboox::registerBox(
            (string)$this->objectType,
            [
                'title' => [$this, 'panelHeader'],
            ]
        );
    }

    /**
     * Déclaration de la liste des organes de saisie
     *
     * @return \tiFy\Taboox\Taboox::registerNode()
     */
    final  public function tify_taboox_register_node()
    {
        // Définition des onglets de saisie par défaut
        $default_tabs = [
            'general' =>  [
                'id'            => "tFyShopProduct-generalOptions--{$this->objectType}",
                'title'         => __('Général', 'tify'),
                'cb'            => [$this, 'generalPanel'],
                'position'      => 1
            ],
            'inventory' => [
                'id'            => "tFyShopProduct-inventoryOptions--{$this->objectType}",
                'title'         => __('Inventaire', 'tify'),
                'cb'            => [$this, 'inventoryPanel'],
                'position'      => 2
            ],
            'shipping' => [
                'id'            => "tFyShopProduct-shippingOptions--{$this->objectType}",
                'title'         => __('Expédition', 'tify'),
                'cb'            => [$this, 'shippingPanel'],
                'position'      => 3
            ],
            'linked' => [
                'id'            => "tFyShopProduct-linkedOptions--{$this->objectType}",
                'title'         => __('Produits liés', 'tify'),
                'cb'            => [$this, 'linkedPanel'],
                'position'      => 4
            ],
            'attributes' => [
                'id'            => "tFyShopProduct-attributesOptions--{$this->objectType}",
                'title'         => __('Attributs', 'tify'),
                'cb'            => [$this, 'attributesPanel'],
                'position'      => 5
            ],
            'variations' => [
                'id'            => "tFyShopProduct-variationsOptions--{$this->objectType}",
                'title'         => __('Variations', 'tify'),
                'cb'            => [$this, 'variationsPanel'],
                'position'      => 6
            ],
            'advanced'   =>             [
                'id'            => "tFyShopProduct-advancedOptions--{$this->objectType}",
                'title'         => __('Avancé', 'tify'),
                'cb'            => [$this, 'advancedPanel'],
                'position'      => 7
            ]
        ];

        // Récupération des onglets personalisés
        $custom_tabs = $this->objectType->getAttr('tabs', []);

        foreach($default_tabs as $id => $default_tab) :
            if (!isset($custom_tabs[$id])) :
                $custom_tabs[$id] = $default_tab;
            elseif($custom_tabs[$id] !== false) :
                $custom_tabs[$id] = array_merge($default_tab, (array)$custom_tabs[$id]);
            else :
                unset($custom_tabs[$id]);
            endif;
        endforeach;

        foreach($custom_tabs as $attrs) :
            Taboox::registerNode(
                (string)$this->objectType,
                $attrs
            );
        endforeach;
    }

    /**
     * Affichage de l'écran courant
     *
     * @return void
     */
    final public function current_screen($current_screen)
    {
        if ($current_screen->id !== (string)$this->objectType) :
            return;
        endif;

        // Déclenchement de la mise en file des scripts de l'interface d'administration
        $this->appAddAction('admin_enqueue_scripts');
    }

    /**
     * Mise en file des scripts de l'interface d'administration
     *
     * @return void
     */
    final public function admin_enqueue_scripts()
    {
        Field::enqueue('SelectJs');

        \wp_enqueue_script(
            'tFyPluginsShopAdminEdit',
            self::tFyAppUrl(get_class()) . '/Edit.js',
            ['jquery'],
            171219,
            true
        );
        \wp_enqueue_style(
            'tFyPluginsShopAdminEdit',
            self::tFyAppUrl(get_class()) . '/Edit.css',
            [],
            171219
        );
    }

    /**
     * CONTROLEURS
     */
    /**
     * Titre du panneau de saisie
     *
     * @return string
     */
    final public function panelHeader($post)
    {
        $product = $this->shop->products()->get($post);

        $product_type_selector = '';
        if ($product_types = $product->getProductTypes()) :
            $product_type_options = [];
            foreach ($product_types as $product_type) :
                $product_type_options[$product_type] = $this->shop->products()->getProductTypeDisplayName($product_type);
            endforeach;

            $product_type_selector .= '<b> — </b>';
            $product_type_selector .= Field::Select(
                [
                    'name'     => 'product-type',
                    'value'    => $product->getProductType(),
                    'options'  => $product_type_options
                ]
            );
        else :
            $product_type_selector .= Field::Hidden(
                [
                    'name'    => 'product-type',
                    'options' => 'simple'
                ]
            );
        endif;

        echo '<b>' . __('Données produit', 'tify') . '</b>' . $product_type_selector;
    }

    /**
     * Saisie des options générales
     *
     * @param \WP_Post $post Object Post Wordpress
     *
     * @return string
     */
    final public function generalPanel($post)
    {
        $product = $this->shop->products()->get($post);

        self::tFyAppGetTemplatePart(
            'general',
            (string)$this->objectType,
            compact('post', 'product')
        );
    }

    /**
     * Saisie des données d'inventaire
     *
     * @param \WP_Post $post Object Post Wordpress
     *
     * @return string
     */
    final public function inventoryPanel($post)
    {
        $product = $this->shop->products()->get($post);

        self::tFyAppGetTemplatePart(
            'inventory',
            (string)$this->objectType,
            compact('post', 'product')
        );
    }

    /**
     * Saisie des données de livraison
     *
     * @param \WP_Post $post Object Post Wordpress
     *
     * @return string
     */
    final public function shippingPanel($post)
    {
        $product = $this->shop->products()->get($post);

        self::tFyAppGetTemplatePart(
            'shipping',
            (string)$this->objectType,
            compact('post', 'product')
        );
    }

    /**
     * Saisie des données de produits liés
     *
     * @param \WP_Post $post Object Post Wordpress
     *
     * @return string
     */
    final public function linkedPanel($post)
    {
        $product = $this->shop->products()->get($post);

        self::tFyAppGetTemplatePart(
            'linked',
            (string)$this->objectType,
            compact('post', 'product')
        );
    }

    /**
     * Saisie des données d'attributs
     *
     * @param \WP_Post $post Object Post Wordpress
     *
     * @return string
     */
    final public function attributesPanel($post)
    {
        $product = $this->shop->products()->get($post);

        self::tFyAppGetTemplatePart(
            'attributes',
            (string)$this->objectType,
            compact('post', 'product')
        );
    }

    /**
     * Saisie des données de variations
     *
     * @param \WP_Post $post Object Post Wordpress
     *
     * @return string
     */
    final public function variationsPanel($post)
    {
        $product = $this->shop->products()->get($post);

        self::tFyAppGetTemplatePart(
            'variations',
            (string)$this->objectType,
            compact('post', 'product')
        );
    }

    /**
     * Saisie des données de advanced
     *
     * @param \WP_Post $post Object Post Wordpress
     *
     * @return string
     */
    final public function advancedPanel($post)
    {
        $product = $this->shop->products()->get($post);

        self::tFyAppGetTemplatePart(
            'advanced',
            (string)$this->objectType,
            compact('post', 'product')
        );
    }
}