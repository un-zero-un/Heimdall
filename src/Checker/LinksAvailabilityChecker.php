<?php

declare(strict_types=1);

namespace App\Checker;

use App\Model\Site;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LinksAvailabilityChecker implements Checker
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function check(Site $site, array $config = []): iterable
    {
        $parsedUrl = parse_url($site->getUrl());
        $this->parseUrl($parsedUrl['host'], $site->getUrl(), []);

        return [];
    }

    private function parseUrl(string $host, string $uri, array $parsedUrls): array
    {
        try {
            $response = $this->httpClient->request('GET', $host . $uri);
        } catch (\Exception $e) {
            return [new CheckResult('error', 'no_links_to_parse_site_is_down')];
        }

        if ($response->getStatusCode() >= 400) {
            return [new CheckResult('warning', 'link_status_is_errored', ['status' => $response->getStatusCode()])];
        }
    }
}
