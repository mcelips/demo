<?php

namespace App\Services\DB;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Events\Dispatcher;
use Throwable;

class DB
{

    /**
     * Bootstrap Eloquent so it is ready for usage
     *
     * @return void
     */
    public static function init(): void
    {
        $capsule = new Capsule;

        $databases = config('database');
        foreach ($databases as $name => $config) {
            $capsule->addConnection(array_merge(['driver' => 'mysql'], $config), $name);
        }

        // Set the event dispatcher used by Eloquent models... (optional)
        $capsule->setEventDispatcher(new Dispatcher(new Container));

        // Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();

        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();
    }

    /**
     * Get a new raw query expression.
     *
     * @param mixed $value
     *
     * @return Expression
     */
    public static function raw(mixed $value): Expression
    {
        return Capsule::connection()->raw($value);
    }

    /**
     * Begin a fluent query against a database table.
     *
     * @param string|Closure|Builder $table
     * @param string|null            $as
     *
     * @return Builder
     */
    public static function table(string|Closure|Builder $table, ?string $as = null): Builder
    {
        return Capsule::connection()->query()->from($table, $as);
    }

    /**
     * Execute a Closure within a transaction.
     *
     * @param Closure $callback
     * @param int     $attempts
     *
     * @return void
     * @throws Throwable
     */
    public static function transaction(Closure $callback, int $attempts = 1): void
    {
        Capsule::connection()->transaction($callback, $attempts);
    }

    /**
     * Start a new database transaction.
     *
     * @return void
     * @throws Throwable
     */
    public static function beginTransaction(): void
    {
        Capsule::connection()->beginTransaction();
    }

    /**
     * Commit the active database transaction.
     *
     * @return void
     * @throws Throwable
     */
    public static function commit(): void
    {
        Capsule::connection()->commit();
    }

    /**
     * Rollback the active database transaction.
     *
     * @param int|null $toLevel
     *
     * @return void
     * @throws Throwable
     */
    public static function rollback(?int $toLevel = null): void
    {
        Capsule::connection()->rollBack($toLevel);
    }

}