<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\FileSystem;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\ChangelogLinker;
use Symplify\ChangelogLinker\Configuration\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileSystem;

/**
 * @see \Symplify\ChangelogLinker\Tests\FileSystem\ChangelogFileSystem\ChangelogFileSystemTest
 */
final class ChangelogFileSystem
{
    /**
     * @var ChangelogLinker
     */
    private $changelogLinker;

    /**
     * @var ChangelogPlaceholderGuard
     */
    private $changelogPlaceholderGuard;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(
        ChangelogLinker $changelogLinker,
        ChangelogPlaceholderGuard $changelogPlaceholderGuard,
        FileSystemGuard $fileSystemGuard,
        ParameterProvider $parameterProvider,
        SmartFileSystem $smartFileSystem
    ) {
        $this->changelogLinker = $changelogLinker;
        $this->changelogPlaceholderGuard = $changelogPlaceholderGuard;
        $this->parameterProvider = $parameterProvider;
        $this->fileSystemGuard = $fileSystemGuard;
        $this->smartFileSystem = $smartFileSystem;
    }

    public function readChangelog(): string
    {
        $changelogFilePath = $this->getChangelogFilePath();
        $this->fileSystemGuard->ensureFileExists($changelogFilePath, __METHOD__);

        return $this->smartFileSystem->readFile($changelogFilePath);
    }

    public function storeChangelog(string $content): void
    {
        $this->smartFileSystem->dumpFile($this->getChangelogFilePath(), $content);
    }

    public function addToChangelogOnPlaceholder(string $newContent, string $placeholder): void
    {
        $changelogContent = $this->readChangelog();

        $this->changelogPlaceholderGuard->ensurePlaceholderIsPresent($changelogContent, $placeholder);

        if (Strings::contains($changelogContent, $placeholder)) {
            $newContent = str_replace($placeholder, '', $newContent);
        }

        $contentToWrite = sprintf('%s%s%s%s', $placeholder, PHP_EOL, PHP_EOL, $newContent);

        $updatedChangelogContent = str_replace($placeholder, $contentToWrite, $changelogContent);
        $updatedChangelogContent = $this->changelogLinker->processContentWithLinkAppends($updatedChangelogContent);
        $updatedChangelogContent = str_replace(PHP_EOL . PHP_EOL . ' -', PHP_EOL . ' -', $updatedChangelogContent);
        $updatedChangelogContent = str_replace(
            $placeholder . PHP_EOL . ' -',
            $placeholder . PHP_EOL . PHP_EOL . ' -',
            $updatedChangelogContent
        );

        $this->storeChangelog($updatedChangelogContent);
    }

    private function getChangelogFilePath(): string
    {
        $fileParameter = $this->parameterProvider->provideParameter(Option::FILE);
        if (is_string($fileParameter) && file_exists($fileParameter)) {
            return $fileParameter;
        }

        return getcwd() . '/CHANGELOG.md';
    }
}
