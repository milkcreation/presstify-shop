<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Controller;

use tiFy\Routing\BaseController;
use tiFy\Plugins\Shop\{
    Contracts\ApiController as ApiControllerContract,
    ShopAwareTrait
};
use tiFy\Support\Proxy\{Request, Url};

class ApiController extends BaseController implements ApiControllerContract
{
    use ShopAwareTrait;

    /**
     * @inheritDoc
     */
    public function index(): array
    {
        return [
            'body'    => [
                'namespace' => 'shop/api',
                'routes'    => [
                    '/shop/api'        => [
                        'methods' => ['GET'],
                        '_links'  => [
                            'self' => add_query_arg(
                                'authtoken',
                                Request::input('authtoken', 'XXXXXXX'),
                                (string)Url::root('/shop/api')
                            ),
                        ],
                    ],
                    '/shop/api/orders' => [
                        'namespace' => 'shop/api',
                        'methods'   => ['GET', 'POST'],
                        'endpoints' => [
                            [
                                'methods' => ['GET'],
                                'args'    => [
                                    'authtoken'      => [
                                        'required'    => true,
                                        'description' => __('Jeton d\'accès aux resultats.', 'tify'),
                                        'type'        => 'string',
                                    ],
                                    'page'           => [
                                        'required'    => false,
                                        'default'     => 1,
                                        'description' => __(
                                            'Page courante dans la collection.',
                                            'tify'
                                        ),
                                        'type'        => 'integer',
                                    ],
                                    'per_page'       => [
                                        'required'    => false,
                                        'default'     => 10,
                                        'description' => __(
                                            'Nombre maximal d’éléments à renvoyer dans le groupe de résultats.',
                                            'tify'
                                        ),
                                        'type'        => 'integer',
                                    ],
                                    'after'          => [
                                        'required'    => false,
                                        'description' => __(
                                            'Limitation du jeu de résultats aux éléments créés après une date ' .
                                            'définie à la norme ISO8601.',
                                            'tify'
                                        ),
                                        'type'        => 'string',
                                    ],
                                    'before'         => [
                                        'required'    => false,
                                        'description' => __(
                                            'Limitation du jeu de résultats aux éléments créés avant une date ' .
                                            'définie à la norme ISO8601.',
                                            'tify'
                                        ),
                                        'type'        => 'string',
                                    ],
                                    'order'          => [
                                        'required'    => false,
                                        'default'     => 'desc',
                                        'description' => __(
                                            'Ordre de tri des éléments.',
                                            'tify'
                                        ),
                                        'type'        => 'string',
                                    ],
                                    'orderby'        => [
                                        'required'    => false,
                                        'default'     => 'id',
                                        'enum'        => [
                                            'id',
                                        ],
                                        'description' => __(
                                            'Attributs d\'ordonnancement des éléments.',
                                            'tify'
                                        ),
                                        'type'        => 'string',
                                    ],
                                    'status'         => [
                                        'required'    => false,
                                        'default'     => 'processing',
                                        'items'       => [
                                            'enum' => [
                                                'cancelled',
                                                'completed',
                                                'failed',
                                                'on-hold',
                                                'pending',
                                                'processing',
                                                'any',
                                            ],
                                            'type' => 'string',
                                        ],
                                        'description' => __(
                                            'Limitation du jeu de résultat au statut des éléments.',
                                            'tify'
                                        ),
                                        'type'        => 'array',
                                    ],
                                    'shop'           => [
                                        'required'    => false,
                                        'description' => __(
                                            'Limitation du jeu de résultats aux identifiants pixvert des boutiques.',
                                            'tify'
                                        ),
                                        'type'        => 'array',
                                        'items'       => [
                                            'type' => 'string',
                                        ],
                                    ],
                                    'transaction_id' => [
                                        'required'    => false,
                                        'description' => __(
                                            'Limitation du jeu de résultats aux identifiant de transaction' .
                                            ' fournis par la plateforme de paiement.',
                                            'tify'
                                        ),
                                        'type'        => 'array',
                                        'items'       => [
                                            'type' => 'integer',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        '_links'    => [
                            'self' => add_query_arg(
                                'authtoken',
                                Request::input('authtoken', 'XXXXXXX'),
                                Url::root('/shop/api/orders')->render()
                            ),
                        ],
                    ],
                ],
            ],
            'headers' => [],
        ];
    }

    public function order(...$args): array
    {
        return $this->shop->resolve('api.endpoint.orders')->handleRequest(...$args);
    }
}