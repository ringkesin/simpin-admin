<?php

namespace App\Services\Push;

use InvalidArgumentException;

class WebPushEndpointPolicy
{
    /** Validate and return an address that must be pinned by the outbound transport. */
    public function resolve(string $endpoint): array
    {
        $url = parse_url($endpoint);
        $host = $url['host'] ?? '';
        $allowed = false;
        foreach (config('push.webpush.allowed_host_suffixes', []) as $suffix) {
            if (str_ends_with($host, '.'.$suffix)) {
                $allowed = true;
            }
        }
        if (strlen($endpoint) > 2048 || ! filter_var($endpoint, FILTER_VALIDATE_URL)
            || ($url['scheme'] ?? '') !== 'https' || ! $allowed
            || isset($url['user']) || isset($url['pass']) || isset($url['fragment'])
            || (isset($url['port']) && $url['port'] !== 443)) {
            throw new InvalidArgumentException('Endpoint Web Push tidak diizinkan.');
        }

        $addresses = $this->addresses($host);
        if ($addresses === []) {
            throw new InvalidArgumentException('DNS layanan Web Push tidak tersedia.');
        }
        foreach ($addresses as $address) {
            $packed = @inet_pton($address);
            if (! filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
                || ! $packed || (strlen($packed) === 4 && (ord($packed[0]) === 0 || ord($packed[0]) >= 224))
                || (strlen($packed) === 16 && (ord($packed[0]) & 0xE0) !== 0x20)) {
                throw new InvalidArgumentException('Alamat layanan Web Push tidak diizinkan.');
            }
        }

        return ['host' => $host, 'address' => $addresses[0]];
    }

    protected function addresses(string $host): array
    {
        $records = @dns_get_record($host, DNS_A | DNS_AAAA);

        return array_values(array_filter(array_map(fn ($record) => $record['ip'] ?? $record['ipv6'] ?? null, $records ?: [])));
    }
}
