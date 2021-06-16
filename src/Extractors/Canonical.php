<?php

namespace DNAHash\Extractors;

use DNAHash\Exceptions\InvalidBase;
use Generator;

/**
 * Canonical
 *
 * @category    Bioinformatics
 * @package     andrewdalpino/DNAHash
 * @author      Andrew DalPino
 */
class Canonical implements Extractor
{
    /**
     * The mapping of bases to their complimentary bases.
     *
     * @var int[]
     */
    protected const BASE_COMPLIMENT_MAP = [
        'A' => 'T',
        'T' => 'A',
        'C' => 'G',
        'G' => 'C',
    ];

    /**
     * The base iterator.
     *
     * @var iterable<string>
     */
    protected iterable $iterator;

    /**
     * Return the reverse compliment of a sequence.
     *
     * @param string $sequence
     * @return string
     */
    protected static function reverseCompliment(string $sequence) : string
    {
        $k = strlen($sequence);

        $reverseCompliment = '';

        for ($i = $k - 1; $i >= 0; --$i) {
            $base = $sequence[$i];

            if (!isset(self::BASE_COMPLIMENT_MAP[$base])) {
                throw new InvalidBase($base, self::BASE_COMPLIMENT_MAP);
            }

            $reverseCompliment .= self::BASE_COMPLIMENT_MAP[$base];
        }

        return $reverseCompliment;
    }

    /**
     * @param iterable<string> $iterator
     */
    public function __construct(iterable $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * Return an iterator for the sequences in a dataset.
     *
     * @return \Generator<string>
     */
    public function getIterator() : Generator
    {
        foreach ($this->iterator as $sequence) {
            yield min($sequence, self::reverseCompliment($sequence));
        }
    }
}
