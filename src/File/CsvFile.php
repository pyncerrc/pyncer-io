<?php
namespace Pyncer\IO\File;

use Pyncer\Exception\UnexpectedValueException;
use Pyncer\IO\File\File;

class CsvFile extends File
{
    protected bool $skipEmpty = false;

    public function __construct(
        protected string $delimiter = ',',
        protected string $enclosure = '"',
        protected string $escape = '\\',
    ) {}

    protected function getDelimiter(): string
    {
        return $this->delimiter;
    }

    protected function getEnclosure(): string
    {
        return $this->enclosure;
    }

    protected function getEscape(): string
    {
        return $this->escape;
    }

    public function getSkipEmpty(): bool
    {
        return $this->skipEmpty;
    }
    public function setSkipEmpty(bool $value): static
    {
        $this->skipEmpty = $value;
        return $this;
    }

    /**
     * Gets a row of column values from the CSV file.
     *
     * @param null|int<0, max> $length The length in bytes to read.
     * @return ?array<string>
     */
    public function readRow(?int $length = null): ?array
    {
        if (!$this->isOpen()) {
            throw new UnexpectedValueException('File is not open.');
        }

        if ($this->isEndOfFile()) {
            return null;
        }

        $length ??= $this->getReadLength();

        $row = fgetcsv(
            $this->handle,
            $length,
            $this->delimiter,
            $this->enclosure,
            $this->escape
        );

        if ($row === false) {
            return null;
        }

        // Skip over empty lines
        if ($this->getSkipEmpty() && $row[0] === null) {
            return $this->readRow($length);
        }

        return $row;
    }

    /**
     * Writes a row the the CSV file.
     *
     * @param array<int|string, bool|float|int|string|null> $row An array of column values.
     * @return ?int
     */
    public function writeRow(array $row): ?int
    {
        if (!$this->isOpen()) {
            throw new UnexpectedValueException('File is not open.');
        }

        $result = (fputcsv(
            $this->handle,
            $row,
            $this->delimiter,
            $this->enclosure,
            $this->escape
        ));

        if ($result === false) {
            return null;
        }

        return $result;
    }
}
