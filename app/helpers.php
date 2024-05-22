<?php

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
