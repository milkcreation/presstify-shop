<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Products;

use tiFy\Plugins\Shop\Contracts\{Product,
    Products as ProductsContract,
    ProductObjectType,
    ProductObjectTypeUncategorized,
    Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use WP_Post;

class Products implements ProductsContract
{
    use ShopAwareTrait;

    /**
     * Liste des instances de gammes de produits déclarées.
     * @var ProductObjectTypeUncategorized[]|ProductObjectType[]
     */
    private static $objectTypes = [];

    /**
     * Liste des types de produits permis.
     * @var string[]
     */
    private static $productTypes = [
        'simple',
        'grouped',
        'composed',
        'composing',
        'external',
        'variable',
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);

        foreach ($this->shop()->config('products', []) as $post_type => $attrs) {
            if (empty($attrs['category'])) {
                return self::$objectTypes[$post_type] = $this->shop()->resolve(
                    'products.object-type.uncategorized',
                    [$post_type, $attrs]
                );
            } else {
                return self::$objectTypes[$post_type] = $this->shop()->resolve(
                    'products.object-type.categorized',
                    [$post_type, $attrs]
                );
            }
        }

        add_action('save_post', [$this, 'saveWpPost'], 10, 2);
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }

    /**
     * @inheritDoc
     */
    public function get($id = null): ?Product
    {
        return $this->shop()->resolve('product', [$id]);
    }

    /**
     * @inheritDoc
     */
    public function getObjectType(string $object_type): ?ProductObjectType
    {
        return self::$objectTypes[$object_type] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getObjectTypeList(): array
    {
        return self::$objectTypes;
    }

    /**
     * @inheritDoc
     */
    public function getObjectTypes(): array
    {
        return array_keys(self::$objectTypes);
    }

    /**
     * @inheritDoc
     */
    public function getProductTypeDisplayName(string $product_type): string
    {
        switch ($product_type) {
            default :
                return '';
                break;
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
        }
    }

    /**
     * @inheritDoc
     */
    public function getProductTypeIcon(string $product_type): string
    {
        switch ($product_type) {
            default :
                return '';
                break;
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
        }
    }

    /**
     * @inheritDoc
     */
    public function getProductTypes(): array
    {
        return self::$productTypes;
    }

    /**
     * @inheritDoc
     */
    public function query($query_args = null): ProductsCollection
    {
        return $this->shop()->resolve('products.collection')->query($query_args);
    }

    /**
     * @inheritDoc
     */
    public function saveWpPost(int $post_id, WP_Post $post)
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

        $product = $this->get($post);

        // -----------------------------------------------------------
        // TYPE DE PRODUIT
        $product_type = request()->post('product-type', $product->getProductObjectType()->getDefaultProductType());
        wp_set_post_terms($product->getId(), $product_type, 'product_type');

        // -----------------------------------------------------------
        // VISIBILITE PRODUIT
        $visibility = [];

        // Mise en avant
        $featured = request()->post('_featured', 'off');
        if ($featured === 'on') {
            array_push($visibility, 'featured');
        }

        wp_set_post_terms($product->getId(), $visibility, 'product_visibility');

        return $post;
    }
}