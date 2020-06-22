<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Api\Endpoint;

use League\Fractal\{Manager as FractalManager, Resource\Collection as FractalCollect};
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use tiFy\Plugins\Shop\Api\FractalArraySerializer as DataSerializer;
use tiFy\Plugins\Shop\Contracts\ApiEndpointBaseWpPost as BaseWpPostContract;
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\{DateTime, ParamsBag};
use tiFy\Support\Proxy\Request;
use tiFy\Wordpress\Query\QueryPost;
use tiFy\Wordpress\Contracts\Query\PaginationQuery;

class BaseWpPost extends ParamsBag implements BaseWpPostContract
{
    use ShopAwareTrait;

    /**
     * Instance de la gestionnaire d'arguments de requête de récupération des éléments.
     * @var ParamsBag
     */
    protected $args;

    /**
     * Identifiant de qualification du post.
     * @var int
     */
    protected $id = 0;

    /**
     * Instance du gestionnaire de données.
     * @var FractalManager
     */
    protected $manager;

    /**
     * Instance du gestionnaire de récupération des éléments.
     * @var PaginationQuery
     */
    protected $paginationQuery;

    /**
     * Liste des statuts disponibles.
     * @var array
     */
    protected $statuses = [
        'publish',
        'future',
        'draft',
        'pending',
        'private',
        'trash',
        'auto-draft',
        'inherit'
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->manager = (new FractalManager())->setSerializer(new DataSerializer());
    }

    /**
     * @inheritDoc
     */
    public function args($key = null, $default = null)
    {
        if (!$this->args instanceof ParamsBag) {
            $this->args = new ParamsBag();
        }

        if (!is_null($key)) {
            if (is_string($key)) {
                return $this->args->get($key, $default);
            } elseif (is_array($key)) {
                return $this->args->set($key);
            }
        }

        return $this->args;
    }

    /**
     * @inheritDoc
     */
    public function defaults() : array
    {
        return [
            'after'     => '',
            'before'    => '',
            'order'     => 'DESC',
            'orderby'   => 'ID',
            'per_page'  => 50,
            'page'      => 1,
            'status'    => 'publish'
        ];
    }

    /**
     * @inheritDoc
     */
    public function fetch(): void
    {
        $this->parse();

        if ($id = $this->getId()) {
            $this->args('p', $id);
        } else {
            $this
                ->fetchDateRange()
                ->fetchPage()
                ->fetchPerPage()
                ->fetchOrder()
                ->fetchOrderBy()
                ->fetchStatus();
        }
    }

    /**
     * @inheritDoc
     */
    public function fetchDateRange(): BaseWpPostContract
    {
        $dateQuery = [];
        if ($after = Request::input('after', '')) {
            $dateQuery['after'] = DateTime::parse($after, DateTime::getGlobalTimeZone())->toDateString();
        }

        if ($before = Request::input('before', '')) {
            $dateQuery['before'] = DateTime::parse($before, DateTime::getGlobalTimeZone())->toDateString();
        }

        if ($dateQuery) {
            $dateQuery += ['column'  => 'post_date', 'inclusive' => true];

            $this->args(['date_query.0' => $dateQuery]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchPage(): BaseWpPostContract
    {
        if ($page = Request::input('page', 0)) {
            $this->args(['paged' => $page]);
        } else {
            $this->args(['page' => $this->get('page')]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchPerPage(): BaseWpPostContract
    {
        if ($perPage = Request::input('per_page', 0)) {
            $this->args(['posts_per_page' => $perPage]);
        } else {
            $this->args(['posts_per_page' => $this->get('per_page')]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchOrder(): BaseWpPostContract
    {
        if ($order = strtoupper(Request::input('order', ''))) {
            $this->args(['order' => in_array($order, ['ASC', 'DESC']) ? $order : 'DESC']);
        } else {
            $this->args(['order' => $this->get('order')]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchOrderBy(): BaseWpPostContract
    {
        if ($orderby = Request::input('orderby', '')) {
            $this->args(['orderby' => $orderby]);
        } else {
            $this->args(['orderby' => $this->get('orderby')]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchStatus(): BaseWpPostContract
    {
        if ($status = Request::input('status', '')) {
            $status = ($status === 'any') ? $this->statuses : array_unique(array_map('trim', explode(',', $status)));
            $status = array_intersect($status, $this->statuses);
            $this->args(['post_status' => $status]);
        } else {
            $this->args(['post_status' => $this->get('status')]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function handleRequest(...$args): array
    {
        $this->paginationQuery = null;
        $this->id = $args[0] instanceof ServerRequest ? 0 : (int)$args[0];
        $this->set(Request::all())->parse();

        switch (Request::method()) {
            default :
                return [];
                break;
            case 'GET' :
                return $this->handleRequestGet();
                break;
            case 'POST' :
                return $this->handleRequestPost();
                break;
        }
    }

    /**
     * @inheritDoc
     */
    public function handleRequestGet(): array
    {
        $this->fetch();

        $headers = $this->id ? [] : [
            'total'        => $this->paginationQuery()->getTotal(),
            'total-founds' => $this->paginationQuery()->getCount(),
            'total-pages'  => $this->paginationQuery()->getLastPage(),
        ];

        $datas = $this->paginationQuery()->get('results', []);

        if (Request::input('raw')) {
            $body = $datas;
        } else {
            $resources = new FractalCollect($datas, [$this, 'mapData']);
            $body = $this->manager()->createData($resources)->toArray();
        }

        return compact('body', 'headers');
    }

    /**
     * @inheritDoc
     */
    public function handleRequestPost(): array
    {
        $headers = [];
        $body = [];

        return compact('id', 'body', 'headers');
    }

    /**
     * @inheritDoc
     */
    public function manager(): FractalManager
    {
        return $this->manager;
    }

    /**
     * @inheritDoc
     */
    public function mapData($data): array
    {
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function paginationQuery(): PaginationQuery
    {
        if (is_null($this->paginationQuery)) {
            $queryPost = new QueryPost();
            $queryPost::fetchFromArgs($this->args()->all());

            $this->paginationQuery = $queryPost::pagination();
        }

        return $this->paginationQuery;
    }
}