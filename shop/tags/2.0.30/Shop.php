<?php

namespace tiFy\Plugins\Shop;

use Psr\Container\ContainerInterface;
use tiFy\Plugins\Shop\Contracts\ShopInterface;

/**
 * Class Shop
 *
 * @desc Extension PresstiFy de gestion de boutique en ligne.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package tiFy\Plugins\Shop
 * @version 2.0.30
 *
 * Activation :
 * ----------------------------------------------------------------------------------------------------
 * Dans config/app.php ajouter \tiFy\Plugins\Shop\ShopServiceProvider à la liste des fournisseurs de services
 *     chargés automatiquement par l'application.
 * ex.
 * <?php
 * ...
 * use tiFy\Plugins\Shop\ShopServiceProvider;
 * ...
 *
 * return [
 *      ...
 *      'providers' => [
 *          ...
 *          ShopServiceProvider::class
 *          ...
 *      ]
 * ];
 *
 * Configuration :
 * ----------------------------------------------------------------------------------------------------
 * Dans le dossier de config, créer le fichier shop.php
 * @see /vendor/presstify-plugins/shop/Resources/config/shop.php Exemple de configuration
 */
final class Shop implements ShopInterface
{
    use ShopResolverTrait;

    /**
     * Conteneur d'injection de dépendances.
     * @var ContainerInterface
     */
    protected $container;

    /**
     * CONSTRUCTEUR.
     *
     * @param ContainerInterface $container Conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->shop = $this;
    }

    /**
     *
     */
    public function _resolve($alias, ...$args)
    {
        return app("shop.{$alias}", $args);
    }

    /**
     * Récupération du chemin absolu vers une ressource.
     *
     * @param string $path Chemin relatif vers un sous élément.
     *
     * @return string
     */
    public function resourcesDir($path = '')
    {
        $path = '/Resources/' . ltrim($path, '/');

        return file_exists(__DIR__ . $path) ? __DIR__ . $path : '';
    }

    /**
     * Récupération de l'url absolue vers une ressource.
     *
     * @param string $path Chemin relatif vers un sous élément.
     *
     * @return string
     */
    public function resourcesUrl($path = '')
    {
        $cinfo = class_info($this);
        $path = '/Resources/' . ltrim($path, '/');

        return file_exists($cinfo->getDirname() . $path) ? class_info($this)->getUrl() . $path : '';
    }
}