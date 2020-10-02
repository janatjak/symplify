<?php

declare(strict_types=1);

namespace Symplify\Gerudoc\Contract;

use Symplify\Gerudoc\ValueObject\CodeSample;

interface DocumentedRuleInterface
{
    public function getCategory(): string;

    /**
     * @return CodeSample[]
     */
    public function getCodeSamples(): array;
}
