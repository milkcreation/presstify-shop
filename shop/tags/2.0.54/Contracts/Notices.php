<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use LogicException;

interface Notices extends ShopAwareTrait
{
    /**
     * Résolution de sortie de la classe sous forme d'une chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Ajout d'un message de notification.
     *
     * @param string $message Intitulé du message de notification.
     * @param string $type Type de message de notification success (default)|warning|info|error.
     *
     * @return void
     *
     * @throws LogicException
     */
    public function add(string $message, string $type = 'success'): void;

    /**
     * Suppression de la liste des messages de notification.
     *
     * @return void
     */
    public function clear(): void;

    /**
     * Affichage des messages de notification.
     *
     * @return string
     */
    public function display(): string;
}