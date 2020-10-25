<?php

namespace Cc\Bmsf\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    protected $signature = 'bmsf:install {--name=admin} {--force}';
    protected $description = 'bmsf install command';

    public function handle()
    {
        extract(Arr::only($this->options(), ['name', 'force']));
        $name = strtolower($name);
        $dir = relative_path(app_path(ucfirst($name)));
        $this->context = get_defined_vars();

        define('BMSF_ENTRY', $name);

        if (is_dir($dir) && !$force) {
            $this->error("the \"{$dir}\" directory already exists");
            return;
        }

        $this->generateConfig();

        $this->createDir($dir . '/Controllers');
        $this->createDir($dir . '/Models');
        $this->createDir($dir . '/Middleware');

        $this->compileStub('HomeController', $dir . '/Controllers');
        $this->compileStub('routes', $dir);

        $this->runMigrate('users');
        $this->runMigrate('attachments');

        if (!config('jwt.secret')) {
            $this->call('vendor:publish', ['--provider' => 'Tymon\JWTAuth\Providers\LaravelServiceProvider']);
            $this->call('jwt:secret');
        }

        $this->info('install complete!');
    }

    public function generateConfig()
    {
        extract($this->context);
        $src = file_get_contents(__DIR__ . '/../config.php');
        $src = trim(str_replace('<?php', '', $src));
        $src = str_replace('{{prefix}}', $name, $src);
        $src = preg_replace('/]\s*?;$/', "],\n", $src);
        $src = str_replace('return', "'{$name}' =>", $src);
        $src = preg_replace('/^/m', '    ', $src);
        $config = config('bmsf', []);
        if (empty($config)) {
            $configFile = "<?php\n\nreturn [\n" . $src . "];\n";
        } else {
            $configFile = file_get_contents(config_path('bmsf.php'));
            if (isset($config[$name])) {
                if (!$force) {
                    return;
                }
                while (current($config)) {
                    if (key($config) == $name) {
                        next($config);
                        break;
                    }
                    next($config);
                }
                if (null !== key($config)) {
                    $configFile = preg_replace('/([\'|"]' . $name . '[\'|"]\s*?=>[\s\S]+?,)(\s*?[\'|"]' . key($config) . '[\'|"]\s*?=>)/', trim($src) . '$2', $configFile);
                } else {
                    $configFile = preg_replace('/[\'|"]' . $name . '[\'|"]\s*?=>[\s\S]+?]\s*?;/', ltrim($src) . '];', $configFile);
                }
            } else {
                $configFile = preg_replace('/,?\s*?]\s*?;/', ",\n{$src}];", $configFile);
            }
        }
        if (false !== file_put_contents(config_path('bmsf.php'), $configFile)) {
            $this->info('configuration file writed: config/bmsf.php');
        }
    }

    private function runMigrate($type)
    {
        extract($this->context);
        $migrationsDir = relative_path(database_path('migrations'));
        $migrationFile = $migrationsDir . '/' . Arr::first(
            scandir($migrationsDir),
            fn ($file) => Str::endsWith($file, "create_{$name}_{$type}_table.php"),
            date('Y_m_d_His') . "_create_{$name}_{$type}_table.php"
        );
        if (true === $this->compileStub("create_{$type}_table", $migrationFile)) {
            $this->call('migrate', ['--path' => $migrationFile]);
            if ('users' == $type) {
                $this->call('db:seed', ['--class' => \Cc\Bmsf\Database\Seeds\UsersTableSeeder::class]);
            }
        }
    }

    private function createDir($path)
    {
        !is_dir($path) && mkdir($path, 0755, true) && $this->info('directory created: ' . $path);
    }

    private function compileStub($stub, $dest)
    {
        extract($this->context);
        if (!preg_match('/.+\/.+\..+/', $dest)) {
            $dest .= '/' . $stub . '.php';
        }
        if (!file_exists($dest) || !empty($force)) {
            $content = file_get_contents(__DIR__ . '/stubs/' . $stub . '.stub');
            $content = preg_replace_callback(
                '/{{(prefix)}}/i',
                fn ($matchs) => ucfirst($matchs[1]) === $matchs[1] ? ucfirst($name) : $name,
                $content
            );
            if (!empty($content) && false !== file_put_contents($dest, $content)) {
                $this->info('file created: ' . $dest);
                return true;
            }
        }
        return false;
    }
}

function relative_path($path)
{
    return str_replace(base_path() . '/', '', $path);
}
