<?php

namespace Cc\Bmsf\Console;

use Illuminate\Console\Command;

class AttachmentLinkCommand extends Command
{
    protected $signature = 'bmsf:link {--name=admin}';
    protected $description = 'create symbolic link to attachment storage';

    public function handle()
    {
        $name = $this->option('name');
        $root = config('bmsf.' . $name . '.attachment.disk.root');
        if (empty($root)) {
            return $this->error("config \"{$name}.attacent.disk.root\" not exist");
        }
        if (!file_exists(public_path('attachments'))) {
            mkdir(public_path('attachments'), 0755);
        }
        $dir = public_path('attachments/' . $name);
        if (file_exists($dir)) {
            return $this->error("the \"{$dir}\" directory already exists");
        }
        $this->laravel->make('files')->link($root, $dir);
        $this->info("symbolic link created: \"{$dir}\"");
        $this->info("has been linked to: \"{$root}\"");
    }
}
