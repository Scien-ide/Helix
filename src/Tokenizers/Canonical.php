<?php

namespace DNAHash\Tokenizers;

use Generator;

/**
 * Canonical
 *
 * @category    Bioinformatics
 * @package     andrewdalpino/DNAHash
 * @author      Andrew DalPino
 */
class Canonical implements Tokenizer
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
     * The base tokenizer.
     *
     * @var \DNAHash\Tokenizers\Tokenizer
     */
    protected \DNAHash\Tokenizers\Tokenizer $base;

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
            $reverseCompliment .= self::BASE_COMPLIMENT_MAP[$sequence[$i]];
        }

        return $reverseCompliment;
    }

    /**
     * @param \DNAHash\Tokenizers\Tokenizer $base
     */
    public function __construct(Tokenizer $base)
    {
        $this->base = $base;
    }

    /**
     * Tokenize a sequence.
     *
     * @internal
     *
     * @param string $read
     * @return \Generator<string>
     */
    public function tokenize(string $read) : Generator
    {
        $sequences = $this->base->tokenize($read);

        foreach ($sequences as $sequence) {
            yield min($sequence, self::reverseCompliment($sequence));
        }
    }
}
