<?php

declare(strict_types=1);

namespace App\Checker;

use App\Model\Site;
use Symfony\Component\DomCrawler\Crawler;
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
        $parsedUrl   = parse_url($site->getUrl());
        $checkedUrls = [rtrim($site->getUrl(), '/')];

        return $this->checkUrl($parsedUrl['scheme'] ?? 'http', $parsedUrl['host'], $site->getUrl(), $checkedUrls);
    }

    private function checkUrl(string $scheme, string $host, string $url, array $checkedUrls): iterable
    {
        try {
            $response = $this->httpClient->request('GET', $url);

            if ($response->getStatusCode() >= 400) {
                yield new CheckResult('warning', 'link_status_is_errored', ['status' => $response->getStatusCode()]);

                return;
            }

            yield new CheckResult('success', 'link_status_is_ok', ['url' => $url]);

            $crawler = new Crawler($response->getContent());
            foreach ($crawler->filter('a[href]') as $a) {
                $parsed = parse_url($a->getAttribute('href'));
                if (!isset($parsed['path'])) {
                    continue;
                }

                if (isset($parsed['scheme']) && !in_array($parsed['scheme'], ['http', 'https'])) {
                    continue;
                }

                if (isset($parsed['host']) && $parsed['host'] !== $host) {
                    continue;
                }

                $url = rtrim($scheme . '://' . $host . $parsed['path'], '/');
                if (in_array($url, $checkedUrls, true)) {
                    continue;
                }

                $checkedUrls[] = $url;
                yield from $this->checkUrl($scheme, $host, $url, $checkedUrls);
            }
        } catch (\Exception $e) {
            yield new CheckResult('error', 'no_links_to_parse_site_is_down');
        }
    }
}