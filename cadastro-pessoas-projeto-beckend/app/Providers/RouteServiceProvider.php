<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * O caminho para a definição de rotas "web".
     *
     * @var string
     */
    protected $webMiddlewareGroup = 'web';

    /**
     * O caminho para a definição de rotas "api".
     *
     * @var string
     */
    protected $apiMiddlewareGroup = 'api';

    /**
     * Registra as rotas do aplicativo.
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }

    /**
     * Define as rotas para a aplicação web.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware($this->webMiddlewareGroup)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define as rotas para a aplicação API.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')  // Prefixo 'api'
             ->middleware($this->apiMiddlewareGroup)  // Middleware 'api' para autenticação
             ->group(base_path('routes/api.php'));
    }
}
