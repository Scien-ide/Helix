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
     * A list of extractors.
     *
     * @var list<\DNAHash\Extractors\Extractor>
     */
    protected array $extractors;

    /**
     * @param \DNAHash\Extractors\Extractor[] $extractors
     */
    public function __construct(array $extractors)
    {
        $this->extractors = array_values($extractors);
    }

    /**
     * Return an iterator for the records in the data table.
     *
     * @throws \DNAHash\Exceptions\RuntimeException
     * @return \Generator<string>
     */
    public function getIterator() : Generator
    {
        foreach ($this->extractors as $extractor) {
            foreach ($extractor as $record) {
                yield $record;
            }
        }
    }
}
