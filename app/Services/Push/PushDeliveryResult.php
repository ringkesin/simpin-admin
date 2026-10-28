<?php

namespace App\Services\Push;

class PushDeliveryResult
{
    /** @var string[] */
    public array $acceptedDeviceIds = [];

    /** @var array<string, string> */
    public array $failedDeviceErrors = [];

    /** @var string[] */
    public array $invalidDeviceIds = [];

    public function accept(string $deviceId): void
    {
        $this->acceptedDeviceIds[] = $deviceId;
    }

    public function fail(string $deviceId, string $message, bool $invalid = false): void
    {
        $this->failedDeviceErrors[$deviceId] = mb_substr($message, 0, 1000);

        if ($invalid) {
            $this->invalidDeviceIds[] = $deviceId;
        }
    }

    public function merge(self $other): void
    {
        $this->acceptedDeviceIds = array_merge($this->acceptedDeviceIds, $other->acceptedDeviceIds);
        $this->failedDeviceErrors = array_merge($this->failedDeviceErrors, $other->failedDeviceErrors);
        $this->invalidDeviceIds = array_merge($this->invalidDeviceIds, $other->invalidDeviceIds);
    }

    public function acceptedCount(): int
    {
        return count($this->acceptedDeviceIds);
    }

    public function failedCount(): int
    {
        return count($this->failedDeviceErrors);
    }
}
