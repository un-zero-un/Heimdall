<?php

declare(strict_types=1);

namespace App\Checker;

use App\Form\Type\CheckerConfiguration\PageDisplaysCorrectlyCheckerConfigType;
use App\Model\Site;
use App\ValueObject\ResultLevel;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PageDisplaysCorrectlyChecker implements Checker, ConfigurableChecker
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
            $response = $this->httpClient->request(
                'GET',
                rtrim($site->getUrl(), '/') . '/' . ltrim($config['page'], '/'),
                [
                    'max_duration' => 10,
                    'headers'      => [
                        'Accept' => '*/*',
                        'User-Agent' => 'Heimdall / Symfony HttpClient'
                    ]
                ]
            );

            if ($response->getStatusCode() >= 400) {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }

        $content = $response->getContent();
        $crawler = new Crawler($content);

        try {
            $text = $crawler->filter($config['selector'])->text();
            if (false === strpos($text, $config['expected'])) {
                return [
                    new CheckResult(
                        ResultLevel::error(),
                        'text_not_found',
                        ['%selector%' => $config['selector'], '%expected%' => $config['expected'], '%page%' => $config['page']]
                    )
                ];
            }
        } catch (\InvalidArgumentException $e) {
            return [new CheckResult(ResultLevel::error(), 'selector_not_found', ['%selector%' => $config['selector'], '%page%' => $config['page']])];
        }

        return [new CheckResult(ResultLevel::success(), 'page_displays_correctly', ['%page%' => $config['page']])];
    }

    public function getDefaultExecutionDelay(): int
    {
        return 60 * 3;
    }

    public static function getName(): string
    {
        return 'page_display_correctly';
    }

    public static function getConfigFormType(): string
    {
        return PageDisplaysCorrectlyCheckerConfigType::class;
    }
}
