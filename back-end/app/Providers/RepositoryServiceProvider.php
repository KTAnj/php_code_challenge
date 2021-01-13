<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\PlayerRepository;
use App\Repositories\PlayerRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All of the repositories bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        PlayerRepositoryInterface::class => PlayerRepository::class,
    ];
}
