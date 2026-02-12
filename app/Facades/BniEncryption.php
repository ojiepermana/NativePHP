<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string encrypt(array $jsonData)
 * @method static array|null decrypt(string $hashedString)
 */
class BniEncryption extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'bni-encryption';
    }
}
