<?php
namespace Pyncer\IO\File;

use Pyncer\Exception\UnexpectedValueException;
use Pyncer\IO\File\File;

use const Pyncer\NL as PYNCER_NL;

class TextFile extends File
{
    /**
     * Reads a line from the file.
     *
     * @param null|int<0, max> $length The length in bytes to read.
     * @return null|string
     */
    public function readLine(?int $length = null): ?string
    {
        if (!$this->isOpen()) {
            throw new UnexpectedValueException('File is not open.');
        }

        if ($this->isEndOfFile()) {
            return null;
        }

        $length ??= $this->getReadLength();

        $result = fgets($this->handle, $length);

        if ($result === false) {
            return null;
        }

        return $result;
    }

    /**
     * Writes a line to the file.
     *
     * @param string $line The line to write.
     * @param null|int<0, max> $length The length in bytes to write.
     * @return null|int The number of bytes written.
     */
    public function writeLine(string $line = '', ?int $length = null): ?int
    {
        return $this->write($line . PYNCER_NL, $length);
    }
}
