<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateFrontendEnums extends Command
{
    protected $signature = 'frontend:enums';

    public function handle(): int
    {
        // Delete any existing generated files.
        $files = Storage::disk('enum-js')->allFiles();

        // Just to ensure this isn't accidentally the wrong directly with non-js files.
        $nonJsFiles = collect($files)->filter(fn ($filename) => preg_match('/\.'.'ts'.'$/', $filename) !== 1);

        if ($nonJsFiles->count() > 0) {
            throw new \Exception('Js enums directory contains non-js files, please check your config.');
        }

        Storage::disk('enum-js')->delete($files);

        $pattern = '/'.collect('App\Enums\*')
            ->map(fn ($item) => str_replace('\\*', '.+', preg_quote($item)))->implode('|').'/';

        $classLoader = require 'vendor/autoload.php';
        $classes = array_unique(array_merge(get_declared_classes(), array_keys($classLoader->getClassMap())));

        // Create a js file for any class that matches the specified pattern.
        foreach ($classes as $class) {
            if (preg_match($pattern, $class) !== 1) {
                continue;
            }
            echo $class;

            $this->writeFile($class);
        }

        return 0;
    }

    protected function writeFile(string $class)
    {
        $outputPath = $class;
        foreach (['App\\Enums\\' => ''] as $pattern => $replacement) {
            $outputPath = preg_replace('/'.preg_quote($pattern).'/', $replacement, $outputPath);
        }
        $outputPath .= '.'.'ts';

        $reflection = new \ReflectionClass($class);

        $is_enum = method_exists($reflection, 'isEnum') && $reflection->isEnum();

        $constants = $reflection->getConstants();

        $outputString = '/* eslint-disable no-unused-vars */';
        $outputString .= "\n";
        $outputString .= 'export enum '.Str::afterLast($class, '\\').' {';
        $outputString .= "\n";
        $i = 0;
        foreach ($constants as $key => $value) {
            if ($is_enum) {
                if (! is_string($value)) {
                    $value = property_exists($value, 'value') ? $value->value : $value->name;
                }
            }

            $outputString .= sprintf('%s = %s', $key, json_encode($value));

            if ($i++ < count($constants) - 1) {
                $outputString .= ',';
            }
            $outputString .= "\n";
        }

        $outputString .= '}';
        $outputString .= "\n";
        $outputString .= '/* eslint-disable no-unused-vars */';
        $outputString .= "\n";

        // Write the output string to the specified path.
        Storage::disk('enum-js')->put($outputPath, $outputString);

        $this->info(sprintf('File written to: %s', $outputPath));
    }
}
