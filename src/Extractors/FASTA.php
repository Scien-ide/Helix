<?php

namespace DNATools\Extractors;

use DNATools\Exceptions\InvalidArgumentException;
use DNATools\Exceptions\RuntimeException;
use Generator;

use function is_dir;
use function is_file;
use function is_readable;
use function substr;
use function trim;
use function fopen;
use function feof;
use function fgets;
use function fclose;

/**
 * FASTA
 *
 * A memory-efficient FASTA dataset extractor.
 *
 * @category    Bioinformatics
 * @package     andrewdalpino/DNATools
 * @author      Andrew DalPino
 */
class FASTA implements Extractor
{
    /**
     * The character that represents the start of the header of a new read.
     *
     * @var string
     */
    private const HEADER_DELIMITER = '>';

    /**
     * The character that represents the start of a comment.
     *
     * @var string
     */
    private const COMMENT_DELIMITER = ';';

    /**
     * The path to the file on disk.
     *
     * @var string
     */
    protected string $path;

    /**
     * @param string $path
     * @throws \DNATools\Exceptions\InvalidArgumentException
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
     * @throws \DNATools\Exceptions\RuntimeException
     * @return \Generator<string>
     */
    public function getIterator() : Generator
    {
        $handle = fopen($this->path, 'r');

        if (!$handle) {
            throw new RuntimeException('Could not open file pointer.');
        }

        rewind($handle);

        $header = $sequence = '';

        while (!feof($handle)) {
            $data = trim(fgets($handle) ?: '');

            if (empty($data)) {
                continue;
            }

            switch ($data[0]) {
                case self::HEADER_DELIMITER:
                    if (!empty($sequence)) {
                        yield $header => $sequence;
                    }

                    $header = substr($data, 1);

                    $sequence = '';

                    break;

                case self::COMMENT_DELIMITER:
                    break;

                default:
                    $sequence .= $data;
            }
        }

        if (!empty($sequence)) {
            yield $header => $sequence;
        }

        fclose($handle);
    }
}
