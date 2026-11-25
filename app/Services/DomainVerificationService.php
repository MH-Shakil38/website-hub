<?php

namespace App\Services;

use App\Models\Domain;

class DomainVerificationService
{
    /**
     * Normalize a raw domain or URL input into a clean lowercase hostname (e.g. "https://WWW.MyShop.com/" -> "myshop.com").
     */
    public function normalizeHostname(string $input): string
    {
        $host = strtolower(trim($input));
        $host = preg_replace('#^https?://#i', '', $host);
        $host = explode('/', $host)[0];
        $host = explode(':', $host)[0];
        $host = preg_replace('/^www\./i', '', $host);

        return trim($host, '. ');
    }

    /**
     * Generate a unique TXT verification token for a custom domain.
     */
    public function generateToken(int $tenantId, string $domain): string
    {
        return 'yourhub-verify=' . substr(hash('sha256', $tenantId . ':' . $domain . ':' . config('app.key')), 0, 28);
    }

    /**
     * Check DNS records (TXT, A, or CNAME) for a custom domain and mark verified if matching.
     * Returns ['verified' => bool, 'method' => string|null, 'message' => string].
     */
    public function verifyDomain(Domain $domainRecord, bool $forceInLocal = false): array
    {
        if ($domainRecord->type === 'subdomain') {
            $domainRecord->update([
                'verified_at' => $domainRecord->verified_at ?? now(),
                'ssl_status' => 'active',
            ]);

            return [
                'verified' => true,
                'method' => 'subdomain',
                'message' => 'Platform subdomain is automatically verified and SSL active.',
            ];
        }

        $host = $domainRecord->domain;
        $expectedToken = $domainRecord->verification_token;
        $expectedIp = (string) config('app.server_ip', '127.0.0.1');
        $expectedCname = strtolower((string) config('app.cname_target', 'sites.multi-website-hub.test'));

        $dnsResult = $this->lookupDns($host, $expectedToken, $expectedIp, $expectedCname);

        if (!$dnsResult['matched'] && $forceInLocal && app()->environment(['local', 'testing'])) {
            $dnsResult = [
                'matched' => true,
                'method' => 'local_simulation',
            ];
        }

        if ($dnsResult['matched']) {
            $domainRecord->update([
                'verified_at' => now(),
                'ssl_status' => 'active',
            ]);

            return [
                'verified' => true,
                'method' => $dnsResult['method'],
                'message' => "Domain [{$host}] verified via {$dnsResult['method']}! Caddy On-Demand SSL is now authorized.",
            ];
        }

        return [
            'verified' => false,
            'method' => null,
            'message' => "DNS records for [{$host}] did not match yet. Please add the TXT record ({$expectedToken}) or point the A record to {$expectedIp}, then try again.",
        ];
    }

    /**
     * Perform actual DNS lookup for TXT, A, and CNAME records.
     */
    protected function lookupDns(string $host, ?string $expectedToken, string $expectedIp, string $expectedCname): array
    {
        try {
            // 1. Check TXT records on _yourhub-challenge.{host} and root {host}
            if ($expectedToken) {
                $txtHosts = ['_yourhub-challenge.' . $host, $host];
                foreach ($txtHosts as $txtHost) {
                    $txtRecords = @dns_get_record($txtHost, DNS_TXT) ?: [];
                    foreach ($txtRecords as $rec) {
                        $txtVal = $rec['txt'] ?? '';
                        if (str_contains($txtVal, $expectedToken)) {
                            return ['matched' => true, 'method' => 'DNS TXT Record'];
                        }
                    }
                }
            }

            // 2. Check A records
            if ($expectedIp !== '' && $expectedIp !== '127.0.0.1') {
                $aRecords = @dns_get_record($host, DNS_A) ?: [];
                foreach ($aRecords as $rec) {
                    if (($rec['ip'] ?? '') === $expectedIp) {
                        return ['matched' => true, 'method' => 'DNS A Record'];
                    }
                }
            }

            // 3. Check CNAME records
            if ($expectedCname !== '') {
                $cnameRecords = @dns_get_record($host, DNS_CNAME) ?: [];
                foreach ($cnameRecords as $rec) {
                    $target = strtolower(rtrim($rec['target'] ?? '', '.'));
                    if ($target === $expectedCname) {
                        return ['matched' => true, 'method' => 'DNS CNAME Record'];
                    }
                }
            }
        } catch (\Throwable) {
            // Ignore DNS network errors and return not matched
        }

        return ['matched' => false, 'method' => null];
    }
}
