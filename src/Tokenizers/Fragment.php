<?php

namespace DNATools\Tokenizers;

use DNATools\Exceptions\InvalidArgumentException;
use Generator;

/**
 * Fragment
 *
 * Generates a non-overlapping fragment of length n from a sequence.
 *
 * !!! note
 *     Fragments that contain invalid bases will not be generated.
 *
 * @category    Bioinformatics
 * @package     Scienide/DNATools
 * @author      Andrew DalPino
 */
class Fragment implements Tokenizer
{
    /**
     * The length of tokenized sequences.
     *
     * @var int
     */
    protected int $n;

    /**
     * The base iterator.
     *
     * @var iterable<string>
     */
    protected iterable $iterator;

    /**
     * The number of fragments that were dropped due to invalid bases.
     *
     * @var int
     */
    protected int $dropped = 0;

    /**
     * @param int $n
     * @param iterable<string> $iterator
     * @throws \DNATools\Exceptions\InvalidArgumentException
     */
    public function __construct(int $n, iterable $iterator)
    {
        if ($n < 1) {
            throw new InvalidArgumentException('N must be'
                . " greater than 1, $n given.");
        }

        $this->n = $n;
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
        foreach ($this->iterator as $sequence) {
            $p = strlen($sequence) - $this->n;

            for ($i = 0; $i <= $p; $i += $this->n) {
                $fragment = substr($sequence, $i, $this->n);

                if (!preg_match('/[^ACTG]/', $fragment)) {
                    yield $fragment;
                }
            }
        }
    }
}
