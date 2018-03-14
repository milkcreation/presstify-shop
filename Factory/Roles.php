<?php
namespace tiFy\Plugins\Shop\Factory;

use \tiFy\Core\User\Role\Role as tFyCoreRoles;

class Roles extends \tiFy\Plugins\Shop\Factory
{
    /**
     * Liste des classes de rappel des rôles
     *
     * @return \tiFy\Core\User\Role\Factory[]
     */
    private static $Roles = [];

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct($id, $attrs = [])
    {
        parent::__construct($id, $attrs);

        // Déclaration des événements
        $this->appAddAction('tify_roles_register');
    }

    /**
     * EVENEMENTS
     */
    /**
     * Déclaration des roles
     *
     * @return
     */
    public function tify_roles_register()
    {
        if ($roles = self::tFyAppConfig('roles', [], 'tiFy\Plugins\Shop\Shop')) :
            foreach ($roles as $role => $attrs) :
                self::$Roles[$role] = tFyCoreRoles::register($role, $attrs);
            endforeach;
        endif;
    }
}