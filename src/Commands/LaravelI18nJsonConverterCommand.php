<?php

namespace ReneRoscher\LaravelI18nJsonConverter\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class LaravelI18nJsonConverterCommand extends Command
{
    protected $signature = 'i18n:convert';

    protected $description = 'Converts Laravel translations to JSON files.';

    public function __construct(
        protected Filesystem $files
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $langPath = resource_path('lang');
        if (! File::exists($langPath)) {
            $langPath = base_path('lang');
        }
        $jsonPath = $langPath.'/json';

        if (! File::exists($jsonPath)) {
            File::makeDirectory($jsonPath, 0755, true);
        }

        foreach ($this->files->directories($langPath) as $localeDir) {
            $locale = basename($localeDir);

            if ($locale === 'json') {
                continue;
            }

            foreach ($this->files->files($localeDir) as $file) {
                if ($file->getExtension() === 'php') {
                    $translations = include $file->getPathname();
                    $flattened = $this->flattenArray($translations);
                    $jsonFileName = $file->getBasename('.php').'.json';
                    $jsonLocalePath = "$jsonPath/$locale";
                    $jsonFilePath = "{$jsonLocalePath}/{$jsonFileName}";

                    if (! File::exists($jsonLocalePath)) {
                        File::makeDirectory($jsonLocalePath, 0755, true);
                    }

                    $this->files->put($jsonFilePath, json_encode($flattened, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    $this->info("Converted $file to $jsonFilePath");
                }
            }
        }

        $this->info('All translations have been converted to JSON.');

        return 0;
    }

    /**
     * Flattens a multi-dimensional array with dot notation.
     */
    protected function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = $prefix.$key;
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey.'.'));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }
}
