<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GenerateUrlCommand extends Command
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('router:generate')
            ->addArgument('route', InputArgument::REQUIRED)
            ->addArgument('route_params', InputArgument::OPTIONAL, '', '{}')
            ->addOption('absolute-url', null, InputOption::VALUE_NONE)
            ->addOption('relative-path', null, InputOption::VALUE_NONE)
            ->addOption('network-path', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH;
        switch (true) {
            case $input->hasOption('absolute-url'):
                $referenceType = UrlGeneratorInterface::ABSOLUTE_URL;
                break;
            case $input->hasOption('relative-path'):
                $referenceType = UrlGeneratorInterface::RELATIVE_PATH;
                break;
            case $input->hasOption('network-path'):
                $referenceType = UrlGeneratorInterface::NETWORK_PATH;
                break;
        }

        $url = $this->urlGenerator->generate(
            $input->getArgument('route'),
            json_decode($input->getArgument('route_params'), true),
            $referenceType
        );

        $io->write(rtrim($url, '/'));
    }
}
