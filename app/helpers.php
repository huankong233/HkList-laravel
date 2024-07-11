<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

if (!function_exists("updateEnv")) {
    /**
     * set the specified configuration value.
     *
     * @param array|string|null $key
     * @param string|null $value
     * @return void
     */
    function updateEnv(array|string|null $key, ?string $value = null): void
    {
        $data = is_array($key) ? $key : [$key => $value];

        $envPath      = base_path(".env");
        $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));

        $contentArray->transform(function ($item) use (&$data) {
            foreach ($data as $key => $value) {
                if (str_starts_with($item, $key . "=")) {
                    unset($data[$key]);
                    if (is_bool($value)) return $key . "=" . ($value ? "true" : "false");
                    return $key . "=" . $value;
                }
            }
            return $item;
        });

        if (count($data) !== 0) {
            $contentArray->add("");
            foreach ($data as $key => $value) {
                $contentArray->add($key . "=" . $value);
            }
        }

        $content = implode("\n", $contentArray->toArray());
        File::put($envPath, $content);
    }
}

if (!function_exists("getVersionString")) {
    /**
     * get Version in the env arr
     * @param $env_arr
     * @return string
     */
    function getVersionString($env_arr): string
    {
        return $env_arr->filter(fn($env, $key) => $key === "_94LIST_VERSION")->first() ?? "0.0.0";
    }
}

if (!function_exists("getEnvFile")) {
    /**
     * get env file arr
     * @param $env_path
     * @return Collection
     */
    function getEnvFile($env_path): Collection
    {
        return collect(explode("\n", File::get($env_path)))
            ->filter(fn($line) => $line)
            ->map(fn($line) => explode("=", $line))
            ->mapWithKeys(fn($item) => [$item[0] => $item[1] ?? ""]);
    }
}