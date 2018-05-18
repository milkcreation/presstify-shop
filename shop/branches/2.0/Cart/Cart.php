<?php

/**
 * @name Cart
 * @desc Gestion du panier d'achat
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Cart
 * @version 1.1
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Arr;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use tiFy\Apps\AppController;
use tiFy\Route\Route;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;

class Cart extends AppController implements CartInterface, ProvideTraitsInterface
{
    use ProvideTraits;

    /**
     * Instance de la classe
     * @var Cart
     */
    private static $instance;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Classe de rappel de collection de la liste des lignes du panier
     * @var LineList
     */
    protected $lines;

    /**
     * Classe de rappel de gestion des données du panier enregistré en session.
     * @var SessionItemsInterface
     */
    protected $sessionItems;

    /**
     * Classe de rappel de calcul des totaux
     * @var Total
     */
    protected $totals;

    /**
     * Liste des messages de notification
     * @var array
     */
    protected $notices = [];

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     *
     * @return void
     */
    private function __construct(Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Déclaration des événements
        $this->appAddAction('after_setup_tify');
        $this->appAddAction('tify_route_register', null, 0);
        $this->appAddAction('wp_loaded', null);
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
     * @return CartInterface
     */
    final public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        self::$instance = new static($shop);

        if(! self::$instance instanceof Cart) :
            throw new LogicException(
                sprintf(
                    __('Le controleur de surcharge doit hériter de %s', 'tify'),
                    Cart::class
                ),
                500
            );
        endif;

        return self::$instance;
    }

    /**
     * A l'issue de l'initialisation complète de presstiFy
     *
     * @return void
     */
    final public function after_setup_tify()
    {
        // Initialisation de la gestion des données de panier d'achat portées par la session
        $this->initSessionItems();

        // Initialisation des messages de notification
        $this->initNotices();
    }

    /**
     * Déclaration du chemin des routes de traitement du panier
     * @internal Ajout d'article au panier|Mise à jour d'article dans le panier|Suppression d'article du panier
     *
     * @param Route $route Classe de rappel de traitement des routes.
     *
     * @return void
     */
    final public function tify_route_register($route)
    {
        // Ajout d'un article au panier
        $route->register(
            'tify.plugins.shop.cart.add',
            [
                'method' => 'post',
                'path'   => '/ajouter-au-panier/{product_name}',
                'cb'     => function (
                    $product_name,
                    ServerRequestInterface $psrRequest,
                    ResponseInterface $psrResponse
                ) {
                    $this->appAddAction(
                        'wp_loaded',
                        function () use ($product_name, $psrRequest, $psrResponse) {
                            call_user_func_array([$this, 'addHandler'], [$product_name, $psrRequest, $psrResponse]);
                        },
                        20
                    );
                }
            ]
        );

        // Mise à jour des articles du panier
        $route->register(
            'tify.plugins.shop.cart.update',
            [
                'method' => 'post',
                'path'   => '/mise-a-jour-du-panier',
                'cb'     => function (ServerRequestInterface $psrRequest, ResponseInterface $psrResponse) {
                    $this->appAddAction(
                        'wp_loaded',
                        function () use ($psrRequest, $psrResponse) {
                            call_user_func_array([$this, 'updateHandler'], [$psrRequest, $psrResponse]);
                        },
                        20
                    );
                }
            ]
        );

        // Suppression d'un article du panier
        $route->register(
            'tify.plugins.shop.cart.remove',
            [
                'method' => ['get', 'post'],
                'path'   => '/supprimer-du-panier/{line_key}',
                'cb'     => function ($line_key, ServerRequestInterface $psrRequest, ResponseInterface $psrResponse) {
                    $this->appAddAction(
                        'wp_loaded',
                        function () use ($line_key, $psrRequest, $psrResponse) {
                            call_user_func_array([$this, 'removeHandler'], [$line_key, $psrRequest, $psrResponse]);
                        },
                        20
                    );
                }
            ]
        );
    }

    /**
     * A l'issue du chargement comple de Wordpress
     *
     * @return void
     */
    final public function wp_loaded()
    {
        $this->sessionItems()->getCart();

        if ($this->functions()->page()->isCart() && ! $this->getList() && ($message = $this->getNotice('is_empty'))) :
            $this->notices()->add(
                $message,
                'info'
            );
        endif;
    }

    /**
     * Initialisation de la session de gestion des données du panier d'achat
     *
     * @return SessionItemsInterface
     */
    private function initSessionItems()
    {
        if ($this->sessionItems) :
            return $this->sessionItems;
        endif;

        $this->sessionItems = $this->provide('cart.session_items');
        if(! $this->sessionItems instanceof SessionItemsInterface) :
            throw new LogicException(
                sprintf(
                    __('Le controleur de surcharge doit implémenter %s', 'tify'),
                    SessionItemsInterface::class
                ),
                500
            );
        endif;

        return $this->sessionItems;
    }

    /**
     * Récupération de la classe de rappel de gestion des éléments du panier d'achat stocké en session.
     *
     * @return SessionItemsInterface
     */
    public function sessionItems()
    {
        return $this->sessionItems;
    }

    /**
     * Initialisation des messages de notification
     *
     * @return void
     */
    public function initNotices()
    {
        $this->notices = array_merge(
            [
                'successfully_added'   => __('L\'article a été ajouté à votre panier avec succès.', 'tify'),
                'successfully_updated' => __('Votre panier a été mis à jour avec succès.', 'tify'),
                'successfully_removed' => __('L\'article a été supprimé de votre panier avec succès.', 'tify'),
                'is_empty'             => __('Votre panier ne contient actuellement aucun article.', 'tify')
            ],
            $this->config('cart.notices', [])
        );
    }

    /**
     * Récupération d'un message de notification
     *
     * @param string $name Nom de qualification du message de notification.
     * @internal successfully_added|successfully_updated|successfully_removed|is_empty
     * @param string $default Valeur de retour par défaut
     * @return string
     */
    public function getNotice($name, $default = '')
    {
        return Arr::get($this->notices, $name, $default);
    }

    /**
     * Url d'action d'ajout d'un produit au panier d'achat
     * @internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture de formulaire ayant pour attribut "method" POST
     *
     * @param null|int|\WP_Post|\tiFy\Plugins\Shop\Products\ProductItemInterface $product Identification du produit. Produit de la page courante|Identifiant WP|Objet Post WP|Objet produit
     *
     * @return string
     */
    public function addUrl($product)
    {
        if (!$product instanceof \tiFy\Plugins\Shop\Products\ProductItemInterface) :
            $product = $this->products()->get($product);
        endif;

        if ($product instanceof \tiFy\Plugins\Shop\Products\ProductItemInterface) :
            return $this->appServiceGet(Route::class)->url('tify.plugins.shop.cart.add', [$product->getSlug()]);
        else :
            return '';
        endif;
    }

    /**
     * Url d'action de mise à jour des produits du panier d'achat
     * @internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture de formulaire ayant pour attribut "method" POST
     *
     * @param null|int|\WP_Post|\tiFy\Plugins\Shop\Products\ProductItemInterface $product Identification du produit. Produit de la page courante|Identifiant WP|Objet Post WP|Objet produit
     *
     * @return string
     */
    public function updateUrl()
    {
        return $this->appServiceGet(Route::class)->url('tify.plugins.shop.cart.update');
    }

    /**
     * Url d'action de suppression d'un produit du panier d'achat
     *
     * @param string $key Identifiant de qualification de la ligne du panier a supprimer
     *
     * @return string
     */
    public function removeUrl($key)
    {
        return $this->appServiceGet(Route::class)->url('tify.plugins.shop.cart.remove', [$key]);
    }

    /**
     * Récupération de la classe de rappel de gestion des lignes du panier
     *
     * @return LineList|LineInterface[]
     */
    public function lines()
    {
        if (is_null($this->lines)) :
            $this->lines = new LineList();
        endif;

        return $this->lines;
    }

    /**
     * Vide la liste complète des lignes du panier
     *
     * @return void
     */
    public function flush()
    {
        $this->lines = new LineList();
    }

    /**
     * Ajout d'une ligne au panier
     *
     * @param string $key Identifiant de qualification de la ligne
     * @param array $attributes Liste des attributs de la ligne
     *
     * @return LineList
     */
    public function add($key, $attributes)
    {
        return $this->lines()->put($key, $this->provide('cart.line', [$this->shop, $this, $attributes]));
    }

    /**
     * Mise à jour d'une ligne du panier
     *
     * @param string $key Identifiant de qualification de la ligne
     * @param array $attributes Liste des attributs de la ligne
     *
     * @return LineList
     */
    public function update($key, $attributes)
    {
        if ($line = $this->get($key)) :
            foreach ($attributes as $key => $value) :
                $line[$key] = $value;
            endforeach;

            $this->lines()->merge([$key => $line]);
        endif;

        return $this->lines();
    }

    /**
     * Suppression d'une ligne du panier
     *
     * @param string $key Identifiant de qualification de la ligne
     *
     * @return mixed
     */
    public function remove($key)
    {
        return $this->lines()->pull($key);
    }

    /**
     * Récupération de la liste des lignes du panier
     *
     * @return array|LineInterface[]
     */
    public function getList()
    {
        return $this->lines()->all();
    }

    /**
     * Récupération d'une ligne du panier
     *
     * @param string $key Identifiant de qualification de la ligne
     *
     * @return array|LineInterface
     */
    public function get($key)
    {
        return $this->lines()->get($key);
    }

    /**
     * Compte le nombre de ligne du panier
     *
     * @return int
     */
    public function count()
    {
        return $this->lines()->count();
    }

    /**
     * Compte le nombre de produits contenus dans le panier
     *
     * @return int
     */
    public function countProducts()
    {
        return $this->lines()->sum('quantity');
    }

    /**
     * Vérifie si le panier est vide
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->lines()->isEmpty();
    }

    /**
     * Détruit complétement le panier.
     *
     * @return void
     */
    public function destroy()
    {
        $this->flush();
        $this->calculate();
        $this->sessionItems()->destroy();
    }

    /**
     * Vérifie si le panier nécessite une livraison.
     *
     * @return bool
     */
    public function needShipping()
    {
        return false;
    }

    /**
     * Vérifie si le panier nécessite un paiement.
     *
     * @return bool
     */
    public function needPayment()
    {
        return $this->totals->getGlobal() > 0;
    }

    /**
     * Compte le poids que représente l'ensemble des ligne de produits du panier
     *
     * @return float
     */
    public function getProductsWeight()
    {
        return $this->lines()->sum(
            function ($item) {
                /** @var LineInterface $item */
                return $result = (float)$item->getProduct()->getWeight() * $item->getQuantity();
            }
        );
    }

    /**
     * Calcul des totaux basés sur le contenu du panier
     *
     * @return Total
     */
    public function calculate()
    {
        return $this->totals = new Total($this->shop, $this);
    }

    /**
     * Récupération des totaux
     *
     * @return Total
     */
    public function getTotals()
    {
        return $this->totals;
    }

    /**
     * Traitement de l'ajout d'un produit au panier
     *
     * @param string $product_name Identifiant de qualification d'url (Slug) du produit
     * @param ServerRequestInterface $psrRequest Requête HTTP Psr-7
     * @param ResponseInterface $psrResponse Requête HTTP Psr-7
     *
     * @return void
     */
    public function addHandler($product_name, ServerRequestInterface $psrRequest, ResponseInterface $psrResponse)
    {
        /**
         * Vérification d'existance du produit et récupération
         * @var \tiFy\Plugins\Shop\Products\ProductItemInterface $product
         */
        if (!$product = $this->products()->get($product_name)) :
            return;
        endif;

        /**
         * Conversion de la requête PSR-7
         * @see https://symfony.com/doc/current/components/psr7.html
         * @var \Symfony\Component\HttpFoundation\Request $request
         */
        $request = (new HttpFoundationFactory())->createRequest($psrRequest);

        // Récupération de la quantité de produit
        if (!$quantity = $request->request->getInt('quantity', 1)) :
            return;
        endif;

        // Vérifie si un produit peut être commandé
        if (!$product->isPurchasable()) :
            return;
        endif;

        // Définition de la ligne du panier
        $key = md5(implode('_', [$product->getid()]));
        if ($exists = $this->get($key)) :
            $quantity += $exists->getQuantity();
        endif;
        $this->add($key, compact('key', 'quantity', 'product'));

        // Mise à jour des données de session
        $this->sessionItems()->update();

        // Message de notification
        if ($message = $this->getNotice('successfully_added')) :
            $this->notices()->add($message);
        endif;

        // Définition de l'url de redirection
        if ($redirect = $request->request->get('_wp_http_referer', '')) :
        elseif ($redirect = $product->getPermalink()) :
        else :
            $redirect = \wp_get_referer();
        endif;

        \wp_redirect(($redirect ?: \get_home_url()));
        exit;
    }

    /**
     * Traitement de la mise à jour des produits du panier
     *
     * @param ServerRequestInterface $psrRequest Requête HTTP Psr-7
     * @param ResponseInterface $psrResponse Requête HTTP Psr-7
     *
     * @return void
     */
    public function updateHandler(ServerRequestInterface $psrRequest, ResponseInterface $psrResponse)
    {
        /**
         * Conversion de la requête PSR-7
         * @see https://symfony.com/doc/current/components/psr7.html
         * @var \Symfony\Component\HttpFoundation\Request $request
         */
        $request = (new HttpFoundationFactory())->createRequest($psrRequest);

        if ($lines = $request->request->get('cart')) :
            foreach ($lines as $key => $attributes) :
                $this->update($key, $attributes);
            endforeach;

            // Mise à jour des données de session
            $this->sessionItems()->update();

            // Message de notification
            if ($message = $this->getNotice('successfully_updated')) :
                $this->notices()->add($message);
            endif;
        endif;

        // Définition de l'url de redirection
        if ($redirect = $request->request->get('_wp_http_referer', '')) :
        elseif ($redirect = $this->functions()->url()->cartPage()) :
        else :
            $redirect = \wp_get_referer();
        endif;

        \wp_redirect(($redirect ?: \get_home_url()));
        exit;
    }

    /**
     * Traitement de la suppression d'un produit du panier
     *
     * @param string $key Identifiant de qualification de la ligne du panier à supprimer
     * @param ServerRequestInterface $psrRequest Requête HTTP Psr-7
     * @param ResponseInterface $psrResponse Requête HTTP Psr-7
     *
     * @return bool
     */
    public function removeHandler($key, ServerRequestInterface $psrRequest, ResponseInterface $psrResponse)
    {
        /**
         * Conversion de la requête PSR-7
         * @see https://symfony.com/doc/current/components/psr7.html
         * @var \Symfony\Component\HttpFoundation\Request $request
         */
        $request = (new HttpFoundationFactory())->createRequest($psrRequest);

        if ($this->remove($key)) :
            // Mise à jour des données de session
            $this->sessionItems()->update();

            // Message de notification
            if ($message = $this->getNotice('successfully_removed')) :
                $this->notices()->add($message);
            endif;
        endif;

        // Définition de l'url de redirection
        if ($redirect = $request->get('_wp_http_referer', '')) :
        elseif ($redirect = $this->functions()->url()->cartPage()) :
        else :
            $redirect = \wp_get_referer();
        endif;

        \wp_redirect(($redirect ?: \get_home_url()));
        exit;
    }
}