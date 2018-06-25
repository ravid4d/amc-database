<?php
namespace AmcLab\AmcDatabase\Providers;

use AmcLab\AmcDatabase\Wrappers\MySqlWrapper;
use Illuminate\Support\ServiceProvider;

class AmcDatabaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        //
        $this->app->bind('db.wrapper.MySqlConnection', MySqlWrapper::class);

    }
}
