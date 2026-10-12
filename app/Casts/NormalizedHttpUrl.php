<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class NormalizedHttpUrl implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return $value;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $url = trim((string) $value);
        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            throw new InvalidArgumentException('The URL must be a valid HTTP or HTTPS URL.');
        }

        $scheme = mb_strtolower($parts['scheme']);
        if (! in_array($scheme, ['http', 'https'], true) || isset($parts['user']) || isset($parts['pass'])) {
            throw new InvalidArgumentException('Only credential-free HTTP and HTTPS URLs are allowed.');
        }

        $host = mb_strtolower($parts['host']);
        $normalized = $scheme.'://'.$host;
        $normalized .= isset($parts['port']) ? ':'.$parts['port'] : '';
        $normalized .= $parts['path'] ?? '';
        $normalized .= isset($parts['query']) ? '?'.$parts['query'] : '';
        $normalized .= isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        if (filter_var($normalized, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('The URL is invalid.');
        }

        return $normalized;
    }
}
