<?php

declare(strict_types=1);

namespace App\Checker;

use App\Model\Site;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IsUpChecker implements Checker
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
            $response = $this->httpClient->request('GET', $site->getUrl());
        } catch (\Exception $e) {
            return [new CheckResult('error', 'site_is_down')];
        }

        if ($response->getStatusCode() >= 400) {
            return [new CheckResult('error', 'site_status_is_errored', ['status' => $response->getStatusCode()])];
        }

        return [new CheckResult('success', 'site_is_up')];
    }

    public function getDefaultExecutionDelay(): int
    {
        return 60;
    }

    public static function getName(): string
    {
        return 'is_up';
    }
}
