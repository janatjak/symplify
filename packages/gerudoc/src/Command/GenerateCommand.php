<?php

declare(strict_types=1);

namespace Symplify\Gerudoc\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Gerudoc\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Finder\SmartFinder;

final class GenerateCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var SmartFinder
     */
    private $smartFinder;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generate documentation for found rules');

        $this->addArgument(
            Option::SOURCE,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more paths to scan for documented rules'
        );

        $this->addOption(Option::OUTPUT_FILE, null, InputOption::VALUE_REQUIRED, 'Output directory path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument(Option::SOURCE);

        // @todo use robot loader for finding classe with interface
        $fileInfos = $this->smartFinder->find($sources, '*.php');
        dump($fileInfos);

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }
}
