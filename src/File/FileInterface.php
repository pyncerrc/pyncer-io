<?php
namespace Pyncer\IO\File;

use Pyncer\IO\File\FileMode;

interface FileInterface
{
    /**
     * Gets the current file handle.
     *
     * @return null|resource
     */
    public function getStream();
    /**
     * Opens a file.
     *
     * @param string $file The file to open.
     * @param \Pyncer\IO\File\FileMode $fileMode The filemode to use.
     * @param array<string, mixed> $params An array of parameters.
     * @return bool True on success, otherwise false.
     */
    public function open(
        ?string $file = null,
        FileMode $fileMode = FileMode::READ_WRITE,
        array $params = [],
    ): bool;
    public function isOpen(): bool;
    public function close(): void;

    public function isEndOfFile(): bool;
    public function truncate(int $size = 0): bool;

    public function read(?int $length = null): ?string;
    public function write(string $value, ?int $length = null): ?int;
}
