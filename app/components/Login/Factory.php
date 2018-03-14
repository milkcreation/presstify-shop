<?php
namespace tiFy\Plugins\Shop\App\Components\Login;

use tiFy\Plugins\Shop\Shop;

class Factory extends \tiFy\Components\Login\Factory
{
    /**
     * Action lancée en cas de succès de connection
     *
     * @param string  $user_login Identifiant de connection
     * @param \WP_User $user Object WP_User de l'utilisateur connecté
     *
     * @return void
     */
    public function on_login_success($user_login, $user)
    {
        parent::on_login_success($user_login, $user);
        
        Shop::session()->clearCookie();
    }

    /**
     * Action lancée en cas de succès de deconnection
     *
     * @return void
     */
    public function on_logout_success()
    {
        parent::on_logout_success();

        Shop::session()->clearCookie();
    }
}