<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use tiFy\Wordpress\Database\Model\{Comment, Commentmeta, Post, Postmeta, Term, Termmeta, User, Usermeta};

class Order extends Post
{
    /**
     * Définition du type de post associé.
     * @var string
     */
    protected $postType = 'shop_order';

    /**
     * @var array
     */
    protected $builtInClasses = [
        Comment::class   => Commentmeta::class,
        Post::class      => Postmeta::class,
        Term::class      => Termmeta::class,
        User::class      => Usermeta::class,
        OrderItem::class => OrderItemmeta::class,
    ];

    /**
     * @var array
     */
    protected $with = ['meta', 'item'];

    /**
     * @return HasMany
     */
    public function item()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}