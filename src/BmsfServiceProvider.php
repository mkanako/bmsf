<?php

namespace Cc\Bmsf;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Route;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BmsfServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Route::aliasMiddleware('Bmsf.auth', Middleware\Authenticate::class);
        Route::aliasMiddleware('Bmsf.permission', Middleware\Permission::class);
        Route::aliasMiddleware('bindings', \Illuminate\Routing\Middleware\SubstituteBindings::class);

        $disks = [];
        $auth = ['guards' => [], 'providers' => []];
        $config = config('bmsf', []);

        if (!empty($config)) {
            $this->app->extend(\App\Exceptions\Handler::class, function ($service, $app) use ($config) {
                $service->renderable(function (\Exception $e, $request) use ($config) {
                    if (defined('BMSF_ENTRY') || in_array(current(explode('/', trim($request->getPathInfo(), '/'))), array_keys($config))) {
                        if ($e instanceof MethodNotAllowedHttpException || $e instanceof NotFoundHttpException) {
                            return err('not Found');
                        }
                        return err($e->getMessage() ?: preg_replace('/.*\\\/', '', get_class($e)));
                    }
                });
                return $service;
            });

            foreach ($config as $key => $value) {
                $auth['guards'][$key] = [
                    'driver' => 'jwt',
                    'provider' => $key,
                ];
                $auth['providers'][$key] = $value['auth']['provider'];
                $disks[$key] = $value['attachment']['disk'];
            }

            config(Arr::dot($auth, 'auth.'));
            config(Arr::dot($disks, 'filesystems.disks.'));

            if (!$this->app->routesAreCached()) {
                foreach ($config as $key => $value) {
                    $this->loadRoutesFrom(app_path(ucfirst($key) . '/routes.php'));
                }
            }
        }

        Response::macro('succ', function ($data = '', $msg = 'success', $code = 0) {
            $data = [
                'msg' => $msg,
                'code' => $code,
                'data' => $data,
            ];
            return $this->json($data);
        });

        Response::macro('err', function ($msg = 'error', $code = 1) {
            $data = [
                'msg' => $msg,
                'code' => $code,
            ];
            return $this->json($data);
        });

        JsonResponse::macro('setJWTHeader', function ($token) {
            if (!$this->headers->get('Authorization')) {
                $this->headers->set('Authorization', 'Bearer ' . $token);
            }
            return $this;
        });

        Route::matched(function ($matched) use ($config) {
            $prefix = $matched->route->action['prefix'];
            if (!empty($prefix) && array_key_exists($prefix, $config)) {
                define('BMSF_ENTRY', $prefix);
                Facades\Auth::macro('newToken', function ($user = null) {
                    if (config('jwt.blacklist_enabled')) {
                        $this->invalidate();
                    }
                    return $this->fromUser($user ?? $this->user);
                });
            }
        });
    }

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\AttachmentLinkCommand::class,
            ]);
        }
    }
}
