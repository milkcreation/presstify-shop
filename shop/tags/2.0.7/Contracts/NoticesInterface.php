<?php

namespace tiFy\Plugins\Shop\Contracts;

use LogicException;
use tiFy\Plugins\Shop\Contracts\BootableControllerInterface;
use tiFy\Plugins\Shop\Contracts\SShopResolverInterface;

interface NoticesInterface extends BootableControllerInterface, ShopResolverInterface
{
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
    public function add($message, $type = 'success');

    /**
     * Suppression de la liste des messages de notification.
     *
     * @return void
     */
    public function clear();

    /**
     * Affichage des messages de notification.
     *
     * @return string
     */
    public function display();
}