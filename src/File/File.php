<?php
namespace Pyncer\IO\File;

use Pyncer\Exception\InvalidArgumentException;
use Pyncer\Exception\UnexpectedValueException;
use Pyncer\IO\File\FileInterface;
use Pyncer\IO\File\FileMode;

class File implements FileInterface
{
    protected string $file;
    protected FileMode $mode;
    /** @var array<string, mixed> **/
    protected array $params;
    /** @var null|resource **/
    protected mixed $handle = null;
    /** @var null|int<0, max> **/
    protected ?int $readLength = null;

    public function __destruct()
    {
        $this->close();
    }

    /**
     * Gets the default length in bytes to read.
     *
     * @return null|int<0, max>
     */
    public function getReadLength(): ?int
    {
        return $this->readLength;
    }

    /**
     * Sets the default length in bytes to read.
     *
     * @param null|int<0, max> $value The value.
     * @return static
     */
    public function setReadLength(?int $value): static
    {
        // @phpstan-ignore-next-line
        if ($value < 0) {
            throw new InvalidArgumentException('Read length is invalid.');
        }

        $this->readLength = $value;

        return $this;
    }

    /**
     * Opens a file.
     *
     * @param string $file The file to open.
     * @param \Pyncer\IO\File\FileMode $mode The filemode to use.
     * @param array<string, mixed> $params An array of parameters.
     * @return bool True on success, otherwise false.
     */
    public function open(
        string $file,
        FileMode $mode = FileMode::READ_WRITE,
        array $params = [],
    ): bool
    {
        $this->file = $file;
        $this->mode = $mode;
        $this->params = $params;

        $fmode = $this->getMode($mode, $params);

        // Ensure any previously opened file is closed
        $this->close();

        $handle = fopen($file, $fmode);

        $this->handle = ($handle ? $handle : null);

        return $this->isOpen();
    }

    /**
     * Gets the string mode to use when calling fopen.
     *
     * @param \Pyncer\IO\File\FileMode $mode The filemode to use.
     * @param array<string, mixed> $params Additional parameters to use.
     * @return string
     */
    protected function getMode(FileMode $mode, array $params): string
    {
        $fmode = '';

        if ($mode === FileMode::WRITE || $mode === FileMode::READ_WRITE) {
            if ($params['truncate'] ?? false) {
                $fmode = 'w';
            } elseif ($params['append'] ?? false) {
                $fmode = 'a';
            } else {
                $fmode = 'c';
            }

            if ($mode === FileMode::READ_WRITE) {
                $fmode .= '+';
            }
        } else {
            $fmode = 'r';
        }

        if ($params['binary'] ?? false) {
            $fmode .= 'b';
        }

        return $fmode;
    }

    public function isOpen(): bool
    {
        return ($this->handle !== null);
    }

    public function close(): void
    {
        if ($this->handle) {
            fclose($this->handle);
            $this->handle = null;
        }
    }

    public function isEndOfFile(): bool
    {
        if ($this->handle === null) {
            throw new UnexpectedValueException('File is not open.');
        }

        return feof($this->handle);
    }

    /**
     * Truncates the file.
     *
     * @param int<0, max> $size The size to truncate to.
     * @return bool True on success, otherwise false.
     */
    public function truncate(int $size = 0): bool
    {
        if ($this->handle === null) {
            throw new UnexpectedValueException('File is not open.');
        }

        return ftruncate($this->handle, $size);
    }

    /**
     * Reads a value from the file.
     *
     * @param null|int<0, max> $length The length in bytes to read.
     * @return null|string The value read.
     */
    public function read(?int $length = null): ?string
    {
        if (!$this->isOpen()) {
            throw new UnexpectedValueException('File is not open.');
        }

        if ($this->isEndOfFile()) {
            return null;
        }

        $length ??= $this->getReadLength() ?? 4096;

        $result = fread($this->handle, $length);

        if ($result === false) {
            return null;
        }

        return $result;
    }

    /**
     * Write a value to the file.
     *
     * @param string $value The value to write.
     * @param null|int<0, max> $length The length in bytes to write.
     * @return null|int The number of bytes written.
     */
    public function write(string $value, ?int $length = null): ?int
    {
        if ($this->handle === null) {
            throw new UnexpectedValueException('File is not open.');
        }

        $result = fwrite($this->handle, $value, $length);

        if ($result === false) {
            return null;
        }

        return $result;
    }
}
