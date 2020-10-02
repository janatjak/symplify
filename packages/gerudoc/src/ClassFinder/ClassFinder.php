<?php

declare(strict_types=1);

namespace Symplify\Gerudoc\ClassFinder;

use Nette\Loaders\RobotLoader;
use Webmozart\Assert\Assert;

final class ClassFinder
{
    /**
     * @param string[] $source
     * @return class-string[]
     */
    public function findInSourceByInterface(array $source, string $interface): array
    {
        Assert::allFileExists($source);

        $robotLoader = $this->createRobotLoaderFromDirectories($source);
        $robotLoader->rebuild();

        $classes = array_keys($robotLoader->getIndexedClasses());

        $classesWithInterface = [];
        foreach ($classes as $class) {
            if (! is_a($class, $interface, true)) {
                continue;
            }

            $classesWithInterface[] = $class;
        }

        return $classesWithInterface;
    }

    /**
     * @param string[] $directories
     */
    private function createRobotLoaderFromDirectories(array $directories): RobotLoader
    {
        $robotLoader = new RobotLoader();
        $robotLoader->addDirectory($directories);
        $robotLoader->ignoreDirs[] = '*/tests/*';
        $robotLoader->ignoreDirs[] = '*/Fixture/*';

        $robotLoader->setTempDirectory(sys_get_temp_dir() . '/class_finder');

        return $robotLoader;
    }
}
