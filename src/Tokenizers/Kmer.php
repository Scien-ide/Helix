<?php

namespace DNAHash\Tokenizers;

use DNAHash\Exceptions\InvalidArgumentException;
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
 * @package     andrewdalpino/DNAHash
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
     * The number of k-mers that were dropped due to invalid bases.
     *
     * @var int
     */
    protected int $dropped = 0;

    /**
     * @param int $k
     * @throws \DNAHash\Exceptions\InvalidArgumentException
     */
    public function __construct(int $k)
    {
        if ($k < 1) {
            throw new InvalidArgumentException('K must be'
                . " greater than 1, $k given.");
        }

        $this->k = $k;
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
     * Tokenize a read.
     *
     * @internal
     *
     * @param string $read
     * @return \Generator<string>
     */
    public function tokenize(string $read) : Generator
    {
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
