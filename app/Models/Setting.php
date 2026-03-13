<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    // -------------------------------------------------------------------------
    //  Static helpers
    // -------------------------------------------------------------------------

    /**
     * Read a setting value by key.
     *
     * @param  string  $key      The setting key to look up.
     * @param  mixed   $default  Returned when the key does not exist in the DB.
     * @return mixed
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $row = static::where('key', $key)->first();

        return $row !== null ? $row->value : $default;
    }

    /**
     * Write (insert or update) a setting value.
     *
     * @param  string  $key
     * @param  mixed   $value  Stored as a string in the `value` column.
     * @return void
     */
    public static function setValue(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Convenience: delete a setting by key.
     *
     * @param  string  $key
     * @return void
     */
    public static function removeKey(string $key): void
    {
        static::where('key', $key)->delete();
    }

    /**
     * Convenience: check if a key exists.
     *
     * @param  string  $key
     * @return bool
     */
    public static function hasKey(string $key): bool
    {
        return static::where('key', $key)->exists();
    }
}
