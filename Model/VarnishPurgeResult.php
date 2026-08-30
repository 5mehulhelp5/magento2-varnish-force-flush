<?php
declare(strict_types=1);

namespace Nx6\VarnishPurge\Model;

final readonly class VarnishPurgeResult
{
    public function __construct(
        public bool $success,
        public string $message,
    ) {
    }
}
