<?php
namespace tiFy\Plugins\Shop\Factory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use tiFy\Core\Db\Db;

class Session extends \tiFy\Plugins\Shop\Factory
{
    /**
     * Classe de rappel de la gestion des requêtes globales
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $Request = null;

    /**
     * Classe de rappel de la base de données de stockage de session
     * @var \tiFy\Core\Db\Factory
     */
    private $Db = null;

    /**
     * Identifiant de qualification du cookie de stockage de session
     * @var string
     */
    private $CookieName = '';

    /**
     * Liste des attributs de qualification de session
     * @var array
     */
    private $SessionAttrs = [];

    /**
     * Liste des variable de session
     * @var array
     */
    private $Data = [];

    /**
     * Vérification d'existance d'une modification des données
     * @var bool
     */
    private $HasChanged = false;

    /**
     * CONSTRUCTEUR
     *
     * @param string $id Identifiant de qualification
     * @param array $attrs Attributs de configuration
     *
     * @return void
     */
    public function __construct($id, $attrs = [])
    {
        parent::__construct($id, $attrs);

        // Définition de l'identifiant de qualification du cookie de stockage de session
        $this->CookieName = 'tify_shop_session_' . COOKIEHASH;

        // Déclaration de la table de base de données
        $this->setDb();

        // Initialisation de la liste des attributs de qualification de la session
        $this->initSessionAttrs();

        // Déclaration des événements de déclenchement
        $this->tFyAppAddAction('wp_loaded', 'setCookie', 10);
        $this->tFyAppAddAction('shutdown');
    }

    /**
     * DECLENCHEURS
     */
    /**
     * A l'issue de l'execution de PHP
     */
    public function shutdown()
    {
        // Sauvegarde des données en base
        $this->saveDbData();
    }

    /**
     * CONTROLEURS
     */
    /**
     * Déclaration de la table de base de données
     * @see https://github.com/kloon/woocommerce-large-sessions
     *
     * @return \tiFy\Core\Db\Factory
     */
    private function setDb()
    {
        $this->Db = Db::register(
            '_tiFyShopSession',
            [
                'install'    => false,
                'name'       => 'tify_shop_sessions',
                'primary'    => 'session_key',
                'col_prefix' => 'session_',
                'meta'       => false,
                'columns'    => [
                    'id'     => [
                        'type'           => 'BIGINT',
                        'size'           => 20,
                        'unsigned'       => true,
                        'auto_increment' => true
                    ],
                    'key'    => [
                        'type' => 'CHAR',
                        'size' => 32,
                        'unsigned'       => false,
                        'auto_increment' => false
                    ],
                    'value'  => [
                        'type' => 'LONGTEXT'
                    ],
                    'expiry' => [
                        'type'     => 'BIGINT',
                        'size'     => 20,
                        'unsigned' => true
                    ]
                ],
                'keys'       => ['session_id' => ['cols' => 'session_id', 'type' => 'UNIQUE']],
            ]
        );
        $this->Db->install();

        return $this->Db;
    }

    /**
     * Récupération de la classe de rappel de la table de base de données
     *
     * @return \tiFy\Core\Db\Factory
     */
    private function getDb()
    {
        return $this->Db;
    }

    /**
     * Récupération de la classe de rappel de la gestion des requêtes globales
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    private function request()
    {
        if (!$this->Request) :
            $this->Request = Request::createFromGlobals();
        endif;

        return $this->Request;
    }

    /**
     * Initialisation de l'identifiant de qualification du client pour les utilisateurs invités ou les utilisateurs authentifiés.
     *
     * @return int|string
     */
    private function initCustomerId()
    {
        if (\is_user_logged_in()) :
            return \get_current_user_id();
        else :
            require_once(ABSPATH . 'wp-includes/class-phpass.php');
            $hasher = new \PasswordHash(8, false);
            return md5($hasher->get_random_bytes(32));
        endif;
    }

    /**
     * Initialisation des attributs de qualification de session
     *
     * @return array
     */
    private function initSessionAttrs()
    {
        $pieces = ['customer_id', 'session_expiration', 'session_expiring', 'cookie_hash'];
        $has_cookie = false;

        $customer_id = $this->initCustomerId();

        if ($cookie_value = $this->request()->cookies->get($this->getCookieName(), '')) :
            $cookie_value = json_decode(rawurldecode($cookie_value), true);

            /**
             * @var string|int $customer_id
             * @var int $session_expiration
             * @var int $session_expiring
             * @var string $cookie_hash
             */
            extract($cookie_value);

            // Contrôle d'intégrité du hashage de cookie
            $hash = $this->getCookieHash($customer_id, $session_expiration);
            if (empty($cookie_hash) || !\hash_equals($hash, $cookie_hash)) :
            else :
                $has_cookie = true;
                $this->Data = $this->getDbData($customer_id);
            endif;
        endif;

        $session_expiration = time()+intval(60*60*48);
        $session_expiring = time()+intval(60*60*47);
        $cookie_hash = $this->getCookieHash($customer_id, $session_expiration);

        if ($has_cookie) :
            $this->updateDbExpiration($customer_id, $session_expiration);
        endif;

        return $this->SessionAttrs = compact($pieces);
    }

    /**
     * Récupération des attributs de qualification de session
     *
     * @param array $pieces Liste des attributs de retour customer_id|session_expiration|session_expiring|cookie_hash
     *
     * @return array
     */
    private function getSessionAttrList($pieces = [])
    {
        // Récupération des attributs de qualification de la session
        if (!$session_attrs = $this->SessionAttrs) :
            return;
        endif;
        extract($session_attrs);

        if (!$pieces) :
            $pieces = ['customer_id', 'session_expiration', 'session_expiring', 'cookie_hash'];
        endif;

        return compact($pieces);
    }

    /**
     * Récupération d'un attribut de qualification de session
     *
     * @param string $name Identifiant de qualification de l'attribut customer_id|session_expiration|session_expiring|cookie_hash
     *
     * @return mixed
     */
    private function getSessionAttr($name, $default = '')
    {
        if ($attr = $this->getSessionAttrList([$name])) :
            return $attr[$name];
        endif;

        return $default;
    }

    /**
     * Récupération de l'identifiant de qualification du cookie de stockage de session
     *
     * @return string
     */
    private function getCookieName()
    {
        return $this->CookieName;
    }

    /**
     * Récupération du hashage de cookie
     *
     * @return string
     */
    private function getCookieHash($customer_id, $session_expiration)
    {
        $to_hash = $customer_id . '|' . $session_expiration;
        return hash_hmac('md5', $to_hash, \wp_hash($to_hash));
    }

    /**
     * Définition d'un cookie de session
     *
     * @param $string $name Identifiant de qualification de l'attribut de session
     * @param $string $value Valeur d'affectation de l'attribut de session
     *
     * @return void
     */
    final public function setCookie()
    {
        $session_attrs = $this->getSessionAttrList();

        $response = new Response();
        $response->headers->setCookie(
            new Cookie(
                $this->getCookieName(),
                rawurlencode(json_encode($session_attrs)),
                time() + 3600,
                ((COOKIEPATH != SITECOOKIEPATH) ? SITECOOKIEPATH : COOKIEPATH),
                COOKIE_DOMAIN,
                ('https' === parse_url(home_url(), PHP_URL_SCHEME))
            )
        );
        $response->send();
    }

    /**
     * Suppression du cookie de session
     *
     * @return void
     */
    final public function clearCookie()
    {
        $response = new Response();
        $response->headers->clearCookie(
            $this->getCookieName(),
            ((COOKIEPATH != SITECOOKIEPATH) ? SITECOOKIEPATH : COOKIEPATH),
            COOKIE_DOMAIN,
            ('https' === parse_url(home_url(), PHP_URL_SCHEME))
        );
        $response->send();
    }

    /**
     *
     *
     * @return int
     */
    final public function getDbId()
    {
        return $this->getDb()->select()->cell('session_id', ['session_key' => $this->getSessionAttr('customer_id')]);
    }

    /**
     * Récupération des variables de session en base de données
     *
     * @return array
     */
    final public function getDbData($customer_id)
    {
        if (defined('WP_SETUP_CONFIG')) :
            return false;
        endif;

        $value = $this->getDb()->select()->cell('session_value', ['session_key' => $customer_id]);

        return maybe_unserialize($value);
    }

    /**
     * Mise à jour
     *
     * @param string|int $customer_id Identifiant de qualification de l'utilisateur
     * @param string $expiration Timestamp d'expiration de la session
     *
     * @return void
     */
    final public function updateDbExpiration($customer_id, $expiration)
    {
        $this->Db->handle()->update(
            $customer_id,
            [
                'session_expiry' => $expiration
            ]
        );
    }

    /**
     * Sauvegarde des variables de session en base de donnée
     *
     * @return void
     */
    final public function saveDbData()
    {
        if (!$this->HasChanged) :
            return false;
        endif;

        $this->Db->handle()->replace(
            [
                'session_key'    => $this->getSessionAttr('customer_id'),
                'session_value'  => maybe_serialize($this->Data),
                'session_expiry' => $this->getSessionAttr('session_expiration')
            ],
            ['%s', '%s', '%d']
        );

        $this->HasChanged = false;
    }

    /**
     * Récupération d'une variable de session.
     *
     * @param string $name Identifiant de qualification de la variable
     * @param mixed $default Valeur de retour par défaut
     *
     * @return array|string
     */
    final public function get($name, $default = '')
    {
        $key = sanitize_key($name);
        return isset($this->Data[$name]) ? \maybe_unserialize($this->Data[$name]) : $default;
    }

    /**
     * Définition d'une variable de session.
     *
     * @param string $name Identifiant de qualification de la variable
     * @param mixed $value Valeur de la variable
     *
     * @return void
     */
    final public function set($name, $value)
    {
        if ($value !== $this->get($name)) :
            $this->Data[$name] = \maybe_serialize($value);
            $this->HasChanged = true;
        endif;
    }
}