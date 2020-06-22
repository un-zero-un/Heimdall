<?php

declare(strict_types=1);

namespace App\Checker;

use App\Model\Site;
use App\ValueObject\ResultLevel;
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
            $response = $this->httpClient->request('GET', $site->getUrl(), ['max_duration' => 10]);

            if ($response->getStatusCode() >= 400) {
                return [
                    new CheckResult(
                        ResultLevel::error(),
                        'site_status_is_errored',
                        [
                            '%status_code%' => $response->getStatusCode(),
                            '%url%'         => $site->getUrl()
                        ]
                    )
                ];
            }
        } catch (\Exception $e) {
            return [new CheckResult(ResultLevel::error(), 'site_is_down')];
        }

        return [new CheckResult(ResultLevel::success(), 'site_is_up')];
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
