<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Products;

use tiFy\Plugins\Shop\Contracts\ProductObjectType as ProductObjectTypeContract;
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\PostType;

class ProductObjectType extends ParamsBag implements ProductObjectTypeContract
{
    use ShopAwareTrait;

    /**
     * Nom de qualification du type de post.
     * @var string
     */
    protected $name = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Identifiant de qualification du type de post.
     * @param array $attrs Attributs de configuration.
     *
     * @return void
     */
    public function __construct(string $name, array $attrs)
    {
        $this->name = $name;

        $this->set($attrs)->parse();

        add_action('init', function () {
            PostType::register($this->getName(), $this->all());

            $single_meta_keys = [
                '_sku', '_regular_price', '_sale_price', '_sale_price_dates_from', '_sale_price_dates_to',
                '_tax_status', '_tax_class', '_manage_stock', '_backorders', '_sold_individually',
                '_weight', '_length', '_width', '_height', '_upsell_ids', '_crosssell_ids',
                '_purchase_note', '_default_attributes', '_virtual', '_downloadable', '_product_image_gallery',
                '_download_limit', '_download_expiry', '_stock', '_stock_status', '_product_version', '_product_attributes',
                '_grouped_products', '_composition_products'
            ];

            foreach ($single_meta_keys as $single_meta_key) {
                PostType::meta()->register($this->getName(), $single_meta_key, true);
            }
        });
    }

    /**
     * Récupération de l'identifiant de qualification du type de post de définition du produit.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'label'     => _x(sprintf('Produits de la gamme %s', $this->getName()), 'post type general name', 'tify'),
            'plural'    => _x(sprintf('Produits de la gamme %s', $this->getName()), 'post type plural name', 'tify'),
            'singular'  => _x(sprintf('Produit de la gamme %s', $this->getName()), 'post type singular name', 'tify'),
            'menu_icon' => 'dashicons-products',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDefaultProductType(): string
    {
        return 'simple';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getProductTypes(): array
    {
        $allowed_types = $this->shop->products()->getProductTypes();

        if (!$product_types = $this->get('product_types', [])) {
            return $allowed_types;
        } else {
            return array_intersect($product_types, $allowed_types);
        }
    }

    /**
     * @inheritDoc
     */
    public function hasCat(): bool
    {
        return $this->get('category', false);
    }

    /**
     * @inheritDoc
     */
    public function parse(): ProductObjectTypeContract
    {
        parent::parse();

        if (($tag = $this->get('tag', false)) && $tag === true) {
            $this->set('taxonomies', ['product_tag']);
        }

        return $this;
    }
}