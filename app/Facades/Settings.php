<?php

namespace App\Facades;

use App\Services\SettingsService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $group, string $key, mixed $default = null)
 * @method static array group(string $group)
 * @method static bool has(string $group, string $key)
 * @method static void set(string $group, string $key, mixed $value, ?string $type = null)
 * @method static void setMany(string $group, array $fields)
 *
 * @see SettingsService
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SettingsService::class;
    }
}
