<?php

declare(strict_types=1);

namespace App\Checker;

use App\Model\Site;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PageDisplaysCorrectlyChecker implements Checker
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
        try {
            $response = $this->httpClient->request('GET', $site->getUrl() . $config['page']);
        } catch (\Exception $e) {
            return [];
        }

        if ($response->getStatusCode() >= 400) {
            return [];
        }

        $crawler = new Crawler($response->getContent());

        try {
            $text = $crawler->filter($config['selector'])->text();
            if (false === strpos($text, $config['expected'])) {
                return [
                    new CheckResult(
                        'error',
                        'text_not_found',
                        ['selector' => $config['selector'], 'expected' => $config['expected'], 'page' => $config['page']]
                    )
                ];
            }
        } catch (\InvalidArgumentException $e) {
            return [new CheckResult('error', 'selector_not_found', ['selector' => $config['selector'], 'page' => $config['page']])];
        }


        return [new CheckResult('success', 'page_displays_correctly', ['page' => $config['page']])];
    }

    public function getDefaultExecutionDelay(): int
    {
        return 60 * 3;
    }
}
