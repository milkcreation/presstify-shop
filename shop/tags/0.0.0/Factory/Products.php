<?php
namespace tiFy\Plugins\Shop\Factory;

class Products extends \tiFy\Plugins\Shop\Factory
{
    /**
     * Liste des classes de rappel des gammes de produits déclarées
     *
     * @return \tiFy\Plugins\Shop\Factory\Products\Product[]|\tiFy\Plugins\Shop\Factory\Products\CategoryProduct[]
     */
    private static $Products = [];

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct($id, $attrs = [])
    {
        parent::__construct($id, $attrs);

        // Déclaration des gammes de produit
        foreach (self::tFyAppConfig('products', [], 'tiFy\Plugins\Shop\Shop') as $id => $attrs) :
            self::register($id, $attrs);
        endforeach;
    }

    /**
     * CONTROLEURS
     */
    /**
     * Déclaration des gammes de produit
     *
     * @param string $id Identifiant de qualification unique
     * @param array $attrs Attributs de configuration
     *
     * @return null|\tiFy\Plugins\Shop\Factory\Products\Product|\tiFy\Plugins\Shop\Factory\Products\CategoryProduct
     */
    public static function register($id, $attrs = [])
    {
        // Gammes de produit unique (sans catégorie)
        if (empty($attrs['category'])) :
            return self::$Products[$id] = new Products\Product($id, $attrs);
        // Gammes de produit multiple
        else :
            return self::$Products[$id] = new Products\CategoryProduct($id, $attrs);
        endif;
    }
}