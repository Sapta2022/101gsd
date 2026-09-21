<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Single read/write path for every value under Super Admin > Settings.
 * Backed by a group+key table with a type cast, so a new setting is
 * added by seeding a row, never a migration. Values are cached as one
 * array per group and invalidated on save. Secrets (type "encrypted")
 * are stored through Laravel's encrypter and never returned in plain
 * form to a form field — callers needing to check "is this set" should
 * use has() rather than reading the value back for display.
 */
class SettingsService
{
    protected const CACHE_PREFIX = 'settings.group.';

    /**
     * Get a single setting value, cast to its stored type.
     */
    public function get(string $group, string $key, mixed $default = null): mixed
    {
        $values = $this->group($group);

        return array_key_exists($key, $values) ? $values[$key] : $default;
    }

    /**
     * Get every setting in a group as [key => castValue].
     */
    public function group(string $group): array
    {
        return Cache::rememberForever(self::CACHE_PREFIX.$group, function () use ($group) {
            return Setting::query()
                ->where('group', $group)
                ->get()
                ->mapWithKeys(fn (Setting $setting) => [
                    $setting->key => $this->castOut($setting),
                ])
                ->all();
        });
    }

    public function has(string $group, string $key): bool
    {
        return array_key_exists($key, $this->group($group));
    }

    /**
     * Set (create or update) one setting. $type only matters on first
     * write for a key; subsequent writes keep using the type already
     * stored unless $type is explicitly passed again.
     */
    public function set(string $group, string $key, mixed $value, ?string $type = null): void
    {
        /** @var Setting $setting */
        $setting = Setting::query()->firstOrNew(['group' => $group, 'key' => $key]);

        $resolvedType = $type ?? $setting->type ?? 'string';
        $setting->type = $resolvedType;
        $setting->value = $this->castIn($value, $resolvedType);
        $setting->save();

        Cache::forget(self::CACHE_PREFIX.$group);
    }

    /**
     * Set many settings in a group at once (e.g. a settings-tab form submit).
     * $fields is [key => ['value' => ..., 'type' => ...]] or [key => value].
     */
    public function setMany(string $group, array $fields): void
    {
        foreach ($fields as $key => $field) {
            if (is_array($field) && array_key_exists('value', $field)) {
                $this->set($group, $key, $field['value'], $field['type'] ?? null);
            } else {
                $this->set($group, $key, $field);
            }
        }
    }

    protected function castOut(Setting $setting): mixed
    {
        return match ($setting->type) {
            'integer' => (int) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode((string) $setting->value, true),
            'encrypted' => $setting->value !== null ? Crypt::decryptString($setting->value) : null,
            default => $setting->value,
        };
    }

    protected function castIn(mixed $value, string $type): ?string
    {
        return match ($type) {
            'json' => json_encode($value),
            'encrypted' => $value !== null && $value !== '' ? Crypt::encryptString((string) $value) : null,
            'boolean' => $value ? '1' : '0',
            default => $value === null ? null : (string) $value,
        };
    }
}
