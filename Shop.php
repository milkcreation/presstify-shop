<?php

/**
 * @name Shop
 * @desc Extension PresstiFy de gestion de boutique en ligne.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop
 * @version 2.0.0
 */

namespace tiFy\Plugins\Shop;

use tiFy\App\Dependency\AbstractAppDependency;
use tiFy\Contracts\App\AppInterface;
use tiFy\Contracts\Container\ContainerInterface;
use tiFy\Plugins\Shop\Contracts\ShopResolverInterface;
use tiFy\Plugins\Shop\ShopResolverTrait;


/**
 * Class Shop
 * @package tiFy\Plugins\Shop
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
final class Shop extends AbstractAppDependency implements ShopResolverInterface
{
    use ShopResolverTrait;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->shop = $this;
    }

    /**
     * Récupération d'une instance de l'application ou d'un service fourni par celle-ci.
     * {@internal Si $abstract est null > Retourne l'instance de l'appication.}
     * {@internal Si $abstract est qualifié > Retourne la résolution du service qualifié.}
     *
     * @param null|string $abstract Nom de qualification du service.
     * @param array $args Liste des variables passé en arguments lors de la résolution du service.
     *
     * @return object|AppInterface|AppContainer
     */
    public function app($abstract = null, $args = [])
    {
        if (is_null($abstract)) :
            return $this->app;
        endif;

        return $this->app->resolve($abstract, $args);
    }
}
