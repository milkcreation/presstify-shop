<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Products;

use tiFy\Plugins\Shop\Contracts\{
    Product,
    Products as ProductsContract,
    ProductsCollection,
    ProductObjectType,
    ProductObjectTypeUncategorized
};
use tiFy\Plugins\Shop\ShopAwareTrait;
use WP_Post;

class Products implements ProductsContract
{
    use ShopAwareTrait;

    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    protected $booted = false;

    /**
     * Liste des instances de gammes de produits déclarées.
     * @var ProductObjectTypeUncategorized[]|ProductObjectType[]|array
     */
    protected $objectType = [];

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
     * @inheritDoc
     */
    public function boot(): ProductsContract
    {
        if (!$this->booted) {
            add_action('save_post', [$this, 'saveWpPost'], 10, 2);

            if ($objectTypes = $this->shop->config('products', [])) {
                foreach ($objectTypes as $name => $attrs) {
                    $this->setObjectType($name, $this->shop->resolve('products.object-type', [$name, $attrs]));
                }
            }

            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function collect(array $products = []): ProductsCollection
    {
        return $this->shop->resolve('products.collection')->set($products);
    }

    /**
     * @inheritDoc
     */
    public function get($id = null): ?Product
    {
        return $this->shop->resolve('product', [$id]);
    }

    /**
     * @inheritDoc
     */
    public function getObjectType(string $name): ?ProductObjectType
    {
        return $this->objectType[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getObjectTypeNames(): array
    {
        return array_keys($this->objectType);
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
    public function query(array $args = []): array
    {
        $product = $this->shop()->product();

        return $product::fetchFromArgs($args) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function saveWpPost(int $post_id, WP_Post $post)
    {
        // Bypass - S'il s'agit d'une routine de sauvegarde automatique.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return null;
        } elseif (defined('DOING_AJAX') && DOING_AJAX) {
            return null;
        } elseif (!$post_type = request()->post('post_type', '')) {
            return null;
        } elseif (('page' === $post_type) && !current_user_can('edit_page', $post_id)) {
            return null;
        } elseif (('page' !== $post_type) && !current_user_can('edit_post', $post_id)) {
            return null;
        } elseif ((!$post = get_post($post_id))) {
            return null;
        } elseif ($post_type !== $post->post_type) {
            return null;
        } elseif (!in_array($post_type, $this->getObjectTypeNames())) {
            return null;
        }

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

    /**
     * @inheritDoc
     */
    public function setObjectType(string $name, ProductObjectType $objectType): ProductsContract
    {
        $this->objectType[$name] = $objectType;

        return $this;
    }
}