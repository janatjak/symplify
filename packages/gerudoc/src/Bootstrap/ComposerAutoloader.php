<?php

declare(strict_types=1);

namespace Symplify\Gerudoc\Bootstrap;

final class ComposerAutoloader
{
    /**
     * @var string[]
     */
    private const POSSIBLE_AUTOOAD_PATHS = [
        // after split package
        __DIR__ . '/../vendor/../autoload.php',
        // dependency
        __DIR__ . '/../../../../autoload.php',
        // monorepo
        __DIR__ . '/../../../../vendor/autoload.php',

        // for sniffs
        __DIR__ . '/../../../../vendor/squizlabs/php_codesniffer/autoload.php',
    ];

    public function load(): void
    {
        $hasAutoload = false;
        foreach (self::POSSIBLE_AUTOOAD_PATHS as $possibleAutoloadPath) {
            if (! file_exists($possibleAutoloadPath)) {
                continue;
            }

            // found it!
            require_once $possibleAutoloadPath;
            $hasAutoload = true;
        }

        if ($hasAutoload) {
            return;
        }

        $message = sprintf(
            'Could not found "vendor/autoload.php" in "%s" paths',
            implode('", ', self::POSSIBLE_AUTOOAD_PATHS)
        );
        exit($message);
    }
}
