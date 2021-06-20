<?php

namespace DNATools\Tokenizers;

use DNATools\Exceptions\InvalidArgumentException;
use Generator;

/**
 * K-mer
 *
 * Generates tokens of length k from DNA sequences containing the bases [A, C, T, G].
 *
 * !!! note
 *     K-mers that contain invalid bases will not be generated.
 *
 * @category    Bioinformatics
 * @package     andrewdalpino/DNATools
 * @author      Andrew DalPino
 */
class Kmer implements Tokenizer
{
    /**
     * The length of tokenized sequences.
     *
     * @var int
     */
    protected int $k;

    /**
     * The base iterator.
     *
     * @var iterable<string>
     */
    protected iterable $iterator;

    /**
     * The number of k-mers that were dropped due to invalid bases.
     *
     * @var int
     */
    protected int $dropped = 0;

    /**
     * @param int $k
     * @param iterable<string> $iterator
     * @throws \DNATools\Exceptions\InvalidArgumentException
     */
    public function __construct(int $k, iterable $iterator)
    {
        if ($k < 1) {
            throw new InvalidArgumentException('K must be'
                . " greater than 1, $k given.");
        }

        $this->k = $k;
        $this->iterator = $iterator;
    }

    /**
     * Return the number of k-mers that were dropped due to invalid bases.
     *
     * @return int
     */
    public function dropped() : int
    {
        return $this->dropped;
    }

    /**
     * Return an iterator for the sequences in a dataset.
     *
     * @return \Generator<string>
     */
    public function getIterator() : Generator
    {
        foreach ($this->iterator as $read) {
            $p = strlen($read) - $this->k;

            for ($i = 0; $i <= $p; ++$i) {
                $sequence = substr($read, $i, $this->k);

                if (preg_match('/[^ACTG]/', $sequence, $matches, PREG_OFFSET_CAPTURE)) {
                    $skip = 1 + (int) $matches[0][1];

                    $i += $skip;

                    $this->dropped += $skip;

                    continue;
                }

                yield $sequence;
            }
        }
    }
}
