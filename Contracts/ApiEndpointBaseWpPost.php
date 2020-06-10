<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use League\Fractal\Manager;
use tiFy\Contracts\Support\ParamsBag;
use tiFy\Wordpress\Contracts\Query\PaginationQuery;

interface ApiEndpointBaseWpPost extends ParamsBag, ShopAwareTrait
{
    /**
     * Instance des arguments de requête de récupération des éléments|Définition|Récupération d'argument.
     *
     * @param string|array|null $key Clé d'indice de l'argument|Liste des définition|null retourne l'instance.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return array
     */
    public function args($key = null, $default = null);

    /**
     * Traitement et récupération des éléments.
     *
     * @return void
     */
    public function fetch(): void;

    /**
     * Récupération de la date de début et de la date de fin.
     *
     * @return static
     */
    public function fetchDateRange(): ApiEndpointBaseWpPost;

    /**
     * Récupération de la page courante.
     *
     * @return static
     */
    public function fetchPage(): ApiEndpointBaseWpPost;

    /**
     * Récupération du nombre d'élément par page.
     *
     * @return static
     */
    public function fetchPerPage(): ApiEndpointBaseWpPost;

    /**
     * Récupération de l'ordre de tri.
     *
     * @return static
     */
    public function fetchOrder(): ApiEndpointBaseWpPost;

    /**
     * Récupération de l'attribut d'ordonnacement.
     *
     * @return static
     */
    public function fetchOrderBy(): ApiEndpointBaseWpPost;

    /**
     * Récupération du statut.
     *
     * @return static
     */
    public function fetchStatus(): ApiEndpointBaseWpPost;

    /**
     * Récupération de l'identifiant de qualification du post.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Traitement des requêtes de récupération des éléments.
     *
     * @param mixed ...$args
     *
     * @return array
     */
    public function handleRequest(...$args): array;

    /**
     * Traitement de la requête GET de récupération des éléments.
     *
     * @return array
     */
    public function handleRequestGet(): array;

    /**
     * Traitement de la requête POST de récupération des éléments.
     *
     * @return array
     */
    public function handleRequestPost(): array;

    /**
     * Instance du gestionnaire.
     *
     * @return Manager
     */
    public function manager();

    /**
     * Cartographie d'une ligne de données.
     *
     * @param mixed $data
     *
     * @return array
     */
    public function mapData($data): array;

    /**
     * @return PaginationQuery
     */
    public function paginationQuery(): PaginationQuery;
}