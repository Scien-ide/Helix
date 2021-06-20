<?php

namespace DNATools\Tokenizers;

use OkBloomer\BloomFilter;
use Generator;

/**
 * Unique
 *
 * Removes duplicate sequences from a dataset while in flight.
 *
 * @category    Bioinformatics
 * @package     Scienide/DNATools
 * @author      Andrew DalPino
 */
class Unique implements Tokenizer
{
    /**
     * The base iterator.
     *
     * @var iterable<string>
     */
    protected iterable $iterator;

    /**
     * The Bloom filter.
     *
     * @var \OkBloomer\BloomFilter
     */
    protected BloomFilter $filter;

    /**
     * The number of sequences that have been dropped so far.
     *
     * @var int
     */
    protected int $dropped = 0;

    /**
     * @param iterable<string> $iterator
     * @param float $maxFalsePositiveRate
     * @param int|null $numHashes
     * @param int $layerSize
     */
    public function __construct(
        iterable $iterator,
        float $maxFalsePositiveRate = 0.01,
        ?int $numHashes = 4,
        int $layerSize = 32000000
    ) {
        $this->iterator = $iterator;
        $this->filter = new BloomFilter($maxFalsePositiveRate, $numHashes, $layerSize);
    }

    /**
     * Return the number of sequences that have been dropped so far.
     *
     * @return int
     */
    public function dropped() : int
    {
        return $this->dropped;
    }

    /**
     * Return an iterator for the records in the data table.
     *
     * @return \Generator<string>
     */
    public function getIterator() : Generator
    {
        foreach ($this->iterator as $sequence) {
            if ($this->filter->existsOrInsert($sequence)) {
                ++$this->dropped;

                continue;
            }

            yield $sequence;
        }
    }
}
