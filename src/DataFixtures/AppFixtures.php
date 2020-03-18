<?php

namespace App\DataFixtures;

use App\Checker\IsUpChecker;
use App\Checker\LinksAvailabilityChecker;
use App\Checker\PageDisplaysCorrectlyChecker;
use App\Model\ConfiguredCheck;
use App\Model\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->createGoogleSite($manager);
        $this->createExampleSite($manager);
        $this->createMdnSite($manager);
        $this->create1z1Site($manager);

        $manager->flush();
    }

    private function createGoogleSite(ObjectManager $manager): Site
    {
        $site = new Site('Google', 'https://www.google.com');

        $configuredCheck1 = new ConfiguredCheck($site, IsUpChecker::class);
        $configuredCheck1->setExecutionDelay(60);

        $configuredCheck2 = new ConfiguredCheck($site, PageDisplaysCorrectlyChecker::class);
        $configuredCheck2->setExecutionDelay(60);
        $configuredCheck2->setConfig([
            'page'     => '/',
            'selector' => 'title',
            'expected' => 'Google',
        ]);

        $manager->persist($site);
        $manager->persist($configuredCheck1);
        $manager->persist($configuredCheck2);

        return $site;
    }

    private function createExampleSite(ObjectManager $manager): Site
    {
        $site = new Site('Example (should fail)', 'https://www.example.com');

        $configuredCheck1 = new ConfiguredCheck($site, IsUpChecker::class);
        $configuredCheck1->setExecutionDelay(60);

        $configuredCheck2 = new ConfiguredCheck($site, PageDisplaysCorrectlyChecker::class);
        $configuredCheck2->setExecutionDelay(60);
        $configuredCheck2->setConfig([
            'page'     => '/',
            'selector' => 'h1',
            'expected' => 'This should fail',
        ]);

        $manager->persist($site);
        $manager->persist($configuredCheck1);
        $manager->persist($configuredCheck2);

        return $site;
    }

    private function createMdnSite(ObjectManager $manager): Site
    {
        $site = new Site('MDN', 'https://developer.mozilla.org');

        $configuredCheck1 = new ConfiguredCheck($site, IsUpChecker::class);
        $configuredCheck1->setExecutionDelay(60);

        $configuredCheck2 = new ConfiguredCheck($site, PageDisplaysCorrectlyChecker::class);
        $configuredCheck2->setExecutionDelay(60);
        $configuredCheck2->setConfig([
            'page'     => '/en-US',
            'selector' => 'h1',
            'expected' => 'Resources for developers, by developers.',
        ]);

        $configuredCheck3 = new ConfiguredCheck($site, PageDisplaysCorrectlyChecker::class);
        $configuredCheck3->setExecutionDelay(300);
        $configuredCheck3->setConfig([
            'page'     => '/en-US/docs/MDN/Getting_started',
            'selector' => 'h1',
            'expected' => 'Getting started on MDN',
        ]);

        $manager->persist($site);
        $manager->persist($configuredCheck1);
        $manager->persist($configuredCheck2);
        $manager->persist($configuredCheck3);

        return $site;
    }

    private function create1z1Site(ObjectManager $manager): Site
    {
        $site = new Site('Un zéro un', 'https://www.un-zero-un.fr');

        $configuredCheck1 = new ConfiguredCheck($site, IsUpChecker::class);
        $configuredCheck1->setExecutionDelay(60);

        $configuredCheck2 = new ConfiguredCheck($site, PageDisplaysCorrectlyChecker::class);
        $configuredCheck2->setExecutionDelay(60);
        $configuredCheck2->setConfig([
            'page'     => '/',
            'selector' => 'h1',
            'expected' => 'L\'agence de com',
        ]);

        $configuredCheck3 = new ConfiguredCheck($site, LinksAvailabilityChecker::class);
        $configuredCheck3->setExecutionDelay(3600);

        $manager->persist($site);
        $manager->persist($configuredCheck1);
        $manager->persist($configuredCheck2);
        $manager->persist($configuredCheck3);

        return $site;
    }
}
