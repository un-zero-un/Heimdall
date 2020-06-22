<?php

declare(strict_types=1);

namespace App\Checker;

use App\Checker\Exception\InvalidUrlException;
use App\Model\Site;
use App\ValueObject\ResultLevel;
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

        if (!isset($parsedUrl['scheme'], $parsedUrl['host'])) {
            throw new InvalidUrlException(sprintf('Missing part in giver url : "%s"', $site->getUrl()));
        }

        return $this->checkUrl($parsedUrl['scheme'] ?? 'http', $parsedUrl['host'], $site->getUrl(), $checkedUrls);
    }

    private function checkUrl(string $scheme, string $host, string $url, array &$checkedUrls): iterable
    {
        $response = null;
        try {
            $response = $this->httpClient->request('GET', $url);

            if ($response->getStatusCode() >= 400) {
                yield new CheckResult(
                    ResultLevel::warning(),
                    'link_status_is_errored',
                    ['%status%' => $response->getStatusCode(), '%url%' => $url]
                );

                return;
            }

            yield new CheckResult(ResultLevel::success(), 'link_status_is_ok', ['%url%' => $url]);

            $crawler = new Crawler($response->getContent());
            foreach ($crawler->filter('a[href]') as $a) {
                /** @var \DOMElement $a */
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
            yield new CheckResult(
                ResultLevel::error(),
                'no_links_to_parse_site_is_down',
                [
                    '%url%'         => $url,
                    '%status_code%' => null !== $response ? $response->getStatusCode() : null,
                ]
            );
        }
    }

    public function getDefaultExecutionDelay(): int
    {
        return 60 * 60 * 24;
    }

    public static function getName(): string
    {
        return 'links_availability';
    }
}
