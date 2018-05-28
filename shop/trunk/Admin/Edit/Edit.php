<?php
namespace tiFy\Plugins\Shop\Admin\Edit;

use tiFy\Apps\AppController;
use tiFy\Field\Field;
use tiFy\TabMetabox\TabMetabox;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\Products\ObjectTypes\Factory;

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
    public function __construct(Shop $shop, Factory $ObjectType)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        $this->objectType = $ObjectType;

        // Déclaration des événements de déclenchement
        $this->appAddAction('tify_tabmetabox_register');
        $this->appAddAction('current_screen');
    }

    /**
     * Déclaration de la liste des organes de saisie.
     *
     * @param TabMetabox $tabMetabox Controleur des boites à onglet de saisie.
     *
     * @return void
     */
    final public function tify_tabmetabox_register($tabMetabox)
    {
        $tabMetabox->registerBox(
            "{$this->objectType}@post_type",
            [
                'title' => [$this, 'panelHeader'],
            ]
        );
        // Définition des onglets de saisie par défaut
        $default_tabs = [
            'general'    => [
                'name'     => "tFyShopProduct-generalOptions--{$this->objectType}",
                'title'    => __('Général', 'tify'),
                'content'  => [$this, 'generalPanel'],
                'position' => 1,
            ],
            'inventory'  => [
                'name'     => "tFyShopProduct-inventoryOptions--{$this->objectType}",
                'title'    => __('Inventaire', 'tify'),
                'content'  => [$this, 'inventoryPanel'],
                'position' => 2,
            ],
            'shipping'   => [
                'name'     => "tFyShopProduct-shippingOptions--{$this->objectType}",
                'title'    => __('Expédition', 'tify'),
                'content'  => [$this, 'shippingPanel'],
                'position' => 3,
            ],
            'linked'     => [
                'name'     => "tFyShopProduct-linkedOptions--{$this->objectType}",
                'title'    => __('Produits liés', 'tify'),
                'content'  => [$this, 'linkedPanel'],
                'position' => 4,
            ],
            'attributes' => [
                'name'     => "tFyShopProduct-attributesOptions--{$this->objectType}",
                'title'    => __('Attributs', 'tify'),
                'content'  => [$this, 'attributesPanel'],
                'position' => 5,
            ],
            'variations' => [
                'name'     => "tFyShopProduct-variationsOptions--{$this->objectType}",
                'title'    => __('Variations', 'tify'),
                'content'  => [$this, 'variationsPanel'],
                'position' => 6,
            ],
            'advanced'   => [
                'name'     => "tFyShopProduct-advancedOptions--{$this->objectType}",
                'title'    => __('Avancé', 'tify'),
                'content'  => [$this, 'advancedPanel'],
                'position' => 7,
            ],
        ];

        // Récupération des onglets personalisés
        $custom_tabs = $this->objectType->get('tabs', []);

        foreach ($default_tabs as $id => $default_tab) :
            if (!isset($custom_tabs[$id])) :
                $custom_tabs[$id] = $default_tab;
            elseif ($custom_tabs[$id] !== false) :
                $custom_tabs[$id] = array_merge($default_tab, (array)$custom_tabs[$id]);
            else :
                unset($custom_tabs[$id]);
            endif;
        endforeach;

        foreach ($custom_tabs as $attrs) :
            $tabMetabox->registerNode(
                "{$this->objectType}@post_type",
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
        $this->appServiceGet(Field::class)->enqueue('SelectJs');

        \wp_enqueue_script(
            'tiFyPluginShopAdminEdit',
            $this->appUrl(get_class()) . '/Edit.js',
            ['jquery'],
            171219,
            true
        );
        \wp_enqueue_style(
            'tiFyPluginShopAdminEdit',
            $this->appUrl(get_class()) . '/Edit.css',
            [],
            171219
        );
    }

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

        return '<b>' . __('Données produit', 'tify') . '</b>' . $product_type_selector;
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

        return $this->appTemplateRender(
            'general',
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

        return $this->appTemplateRender(
            'inventory',
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

        return $this->appTemplateRender(
            'shipping',
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

        return $this->appTemplateRender(
            'linked',
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

        return $this->appTemplateRender(
            'attributes',
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

        return $this->appTemplateRender(
            'variations',
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

        return $this->appTemplateRender(
            'advanced',
            compact('post', 'product')
        );
    }
}