<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExportTranslationCommand extends Command
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('heimdall:export-translations')
            ->addArgument('locale', InputArgument::REQUIRED)
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->translator instanceof TranslatorBagInterface) {
            $io->error('Translator must be an instance of "' . TranslatorBagInterface::class . '"');

            return 1;
        }

        /** @var string $locale */
        $locale = $input->getArgument('locale');
        $json   = 'export default ' . \json_encode(
            $this->translator->getCatalogue($locale)->all('messages'),
            JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
        );

        if (($outputFile = $input->getOption('output')) && is_string($outputFile)) {
            file_put_contents($outputFile, $json);

            return 0;
        }

        $output->writeln($json);

        return 0;
    }
}
