<?php

namespace DNATools\Benchmarks;

use DNATools\DNAHash;
use DNATools\Tokenizers\Kmer;

/**
 * @BeforeMethods({"setUp"})
 */
class DNAToolsBench
{
    private const BASES = [
        'A', 'C', 'G', 'T',
    ];

    private const NUM_READS = 1000;

    private const READ_LENGTH = 700;

    /**
     * @var list<string>
     */
    protected $sequences;

    /**
     * @var \DNATools\DNAHash
     */
    protected $hashTable;

    /**
     * Generate a k-mer of length k.
     *
     * @param int $k
     * @return string
     */
    private static function generateRead(int $k) : string
    {
        $sequence = '';

        for ($i = 0; $i < $k; ++$i) {
            $sequence .= self::BASES[rand(0, 3)];
        }

        return $sequence;
    }

    public function setUp() : void
    {
        $sequences = [];

        for ($i = 0; $i < self::NUM_READS; ++$i) {
            $sequences[] = self::generateRead(self::READ_LENGTH);
        }

        $this->sequences = $sequences;

        $this->hashTable = new DNAHash(0.01, 4);
    }

    /**
     * @Subject
     * @Iterations(5)
     * @OutputTimeUnit("seconds", precision=3)
     */
    public function import() : void
    {
        $this->hashTable->import($this->sequences, new Kmer(25));
    }
}
