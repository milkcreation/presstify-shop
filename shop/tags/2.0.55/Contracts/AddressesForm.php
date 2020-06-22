<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\{Form\FormFactory, Http\Response};

interface AddressesForm extends FormFactory, ShopAwareTrait
{
    /**
     * Procède au traitement des données de formulaire.
     *
     * @return void
     */
    public function proceed(): void;

    /**
     * Récupération de la réponse HTTP.
     *
     * @return Response
     */
    public function response(): Response;

    /**
     * Traitement de validation.
     *
     * @return bool
     */
    public function validate(): bool;

    /**
     * Gabarit d'affichage.
     *
     * @return string
     */
    public function view(): string;
}