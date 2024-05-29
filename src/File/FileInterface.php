<?php
namespace Pyncer\IO\File;

use Pyncer\IO\File\FileMode;

interface FileInterface
{
    public function open(string $file, FileMode $mode = FileMode::READ_WRITE): bool;
    public function isOpen(): bool;
    public function close(): void;

    public function isEndOfFile(): bool;
    public function truncate(int $size = 0): bool;

    public function read(?int $length = null): ?string;
    public function write(string $value, ?int $length = null): ?int;
}
