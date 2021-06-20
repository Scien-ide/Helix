<?php

namespace DNAToolkit\Benchmarks;

use DNAToolkit\DNAHash;

/**
 * @BeforeMethods({"setUp"})
 */
class DNAToolkitBench
{
    private const BASES = [
        'A', 'C', 'G', 'T',
    ];

    private const NUM_SEQUENCES = 100000;

    /**
     * @var list<string>
     */
    protected $sequences;

    /**
     * @var \DNAToolkit\DNAHash
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

        for ($i = 0; $i < self::NUM_SEQUENCES; ++$i) {
            $sequences[] = self::generateRead(25);
        }

        $this->hashTable = new DNAHash(0.001, 3);
    }

    /**
     * @Subject
     * @Iterations(5)
     * @OutputTimeUnit("seconds", precision=3)
     */
    public function import() : void
    {
        $this->hashTable->import($this->sequences);
    }
}
