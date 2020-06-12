<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Plugins\Shop\Contracts\{Addresses, Cart, Checkout, Functions, Gateways, Orders, Products, Session, Settings, Users};

/**
 * @method static Addresses addresses()
 * @method static Cart cart()
 * @method static Checkout checkout()
 * @method static Functions functions()
 * @method static Gateways gateways()
 * @method static Orders orders()
 * @method static Products products()
 * @method static Session session()
 * @method static Settings settings()
 * @method static Users users()
 */
class Shop extends AbstractProxy
{
    public static function getInstanceIdentifier()
    {
        return 'shop';
    }
}