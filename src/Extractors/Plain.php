<?php

namespace DNAHash\Extractors;

use DNAHash\Exceptions\InvalidArgumentException;
use DNAHash\Exceptions\RuntimeException;
use Generator;

use function is_dir;
use function is_file;
use function is_readable;
use function trim;
use function fopen;
use function feof;
use function fgets;
use function fclose;

/**
 * Plain
 *
 * Plain sequence format where sequences are delimitated by a newline character.
 *
 * @category    Bioinformatics
 * @package     andrewdalpino/DNAHash
 * @author      Andrew DalPino
 */
class Plain implements Extractor
{
    /**
     * The path to the file on disk.
     *
     * @var string
     */
    protected string $path;

    /**
     * @param string $path
     * @throws \DNAHash\Exceptions\InvalidArgumentException
     */
    public function __construct(string $path)
    {
        if (empty($path)) {
            throw new InvalidArgumentException('Path cannot be empty.');
        }

        if (is_dir($path)) {
            throw new InvalidArgumentException('Path must be to a file, folder given.');
        }

        if (!is_file($path)) {
            throw new InvalidArgumentException("Path $path is not a file.");
        }

        if (!is_readable($path)) {
            throw new InvalidArgumentException("Path $path is not readable.");
        }

        $this->path = $path;
    }

    /**
     * Return an iterator for the sequences in a dataset.
     *
     * @throws \DNAHash\Exceptions\RuntimeException
     * @return \Generator<string>
     */
    public function getIterator() : Generator
    {
        $handle = fopen($this->path, 'r');

        if (!$handle) {
            throw new RuntimeException('Could not open file pointer.');
        }

        rewind($handle);

        while (!feof($handle)) {
            $sequence = trim(fgets($handle) ?: '');

            if (!empty($sequence)) {
                yield $sequence;
            }
        }

        fclose($handle);
    }
}
