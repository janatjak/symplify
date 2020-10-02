<?php

declare(strict_types=1);

namespace Symplify\Gerudoc\ValueObject;

final class CodeSample
{
    /**
     * @var string
     */
    private $worseCode;

    /**
     * @var string
     */
    private $betterCode;

    public function __construct(string $worseCode, string $betterCode)
    {
        $this->worseCode = $worseCode;
        $this->betterCode = $betterCode;
    }

    public function getWorseCode(): string
    {
        return $this->worseCode;
    }

    public function getBetterCode(): string
    {
        return $this->betterCode;
    }
}
