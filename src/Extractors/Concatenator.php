<?php

namespace DNAHash\Extractors;

use Generator;

/**
 * Concatenator
 *
 * Concatenates the output of multiple extractors.
 *
 * @category    Bioinformatics
 * @package     andrewdalpino/DNAHash
 * @author      Andrew DalPino
 */
class Concatenator implements Extractor
{
    /**
     * A list of iterators.
     *
     * @var list<iterable>
     */
    protected array $iterators;

    /**
     * @param iterable[] $iterators
     */
    public function __construct(array $iterators)
    {
        $this->iterators = array_values($iterators);
    }

    /**
     * Return an iterator for the sequences in a dataset.
     *
     * @return \Generator<string>
     */
    public function getIterator() : Generator
    {
        foreach ($this->iterators as $iterator) {
            foreach ($iterator as $sequence) {
                yield $sequence;
            }
        }
    }
}
