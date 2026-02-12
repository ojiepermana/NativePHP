<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

class GoogleCloudStorageManager
{
    /**
     * @var array<string, GoogleCloudStorageService>
     */
    private array $disks = [];

    /**
     * Get a GCS disk instance.
     */
    public function disk(string $name = 'gcs-upload'): GoogleCloudStorageService
    {
        if (! isset($this->disks[$name])) {
            $this->disks[$name] = $this->createDisk($name);
        }

        return $this->disks[$name];
    }

    /**
     * Create a new disk instance.
     */
    private function createDisk(string $name): GoogleCloudStorageService
    {
        $config = config("filesystems.disks.{$name}");

        if (! $config) {
            throw new InvalidArgumentException("Disk [{$name}] is not configured.");
        }

        return new GoogleCloudStorageService($name);
    }

    /**
     * Dynamically call the default disk instance.
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->disk()->$method(...$parameters);
    }
}
