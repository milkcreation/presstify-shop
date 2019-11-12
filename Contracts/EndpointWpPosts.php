<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Http\Request;
use tiFy\Contracts\Support\ParamsBag;

interface EndpointWpPosts extends ParamsBag, ShopAwareTrait
{
    /**
     * @inheritDoc
     */
    public function endpointGet($id = 0);

    /**
     * @inheritDoc
     */
    public function endpointPost($id = 0);

    /**
     * {@inheritdoc}
     *
     * @param Request $request
     *
     * @return OrdersCollection|Order
     */
    public function getItems(Request $request);

    /**
     * @inheritDoc
     */
    public function getManager();

    /**
     * Traitement de la date de début.
     *
     * @return array
     */
    public function parseAfter();

    /**
     * Traitement de la date de fin.
     *
     * @return array
     */
    public function parseBefore();

    /**
     * Traitement du nombre d'élément par page.
     *
     * @return int
     */
    public function parsePerPage();

    /**
     * Traitement de la page courante.
     *
     * @return int
     */
    public function parsePage();

    /**
     * Traitement de l'ordre de tri.
     *
     * @return string ASC|DESC
     */
    public function parseOrder();

    /**
     * Traitement de l'attribut d'ordonnacement.
     *
     * @return string
     */
    public function parseOrderBy();

    /**
     * Traitement de la liste des statuts de commande.
     *
     * @return array
     */
    public function parseStatus();
}