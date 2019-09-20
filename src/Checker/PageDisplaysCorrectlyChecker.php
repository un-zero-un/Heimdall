<?php

declare(strict_types=1);

namespace App\Checker;

use App\Model\Site;
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
            $response = $this->httpClient->request('GET', $site->getUrl());
        } catch (\Exception $e) {
            return [];
        }

        if ($response->getStatusCode() >= 400) {
            return [];
        }



        return [new CheckResult('success', 'site_is_up')];
    }
}
