<?php

namespace DNAHash\Tests;

use DNAHash\DNAHash;
use DNAHash\Tokenizers\Canonical;
use DNAHash\Tokenizers\Kmer;
use PHPUnit\Framework\TestCase;

/**
 * @group Base
 * @covers \DNAHash\DNAHash
 */
class DNAHashTest extends TestCase
{
    private const BASES = [
        'A', 'C', 'G', 'T',
    ];

    private const NUM_SEQUENCES = 10000;

    private const RANDOM_SEED = 0;

    /**
     * @var \DNAHash\DNAHash
     */
    protected $hashTable;

    /**
     * Generate a read of length k.
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

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->hashTable = new DNAHash(0.001);

        srand(self::RANDOM_SEED);

        $iterator = function () {
            for ($i = 0; $i < self::NUM_SEQUENCES; ++$i) {
                yield self::generateRead(5);
            }
        };

        $this->hashTable->import($iterator(), new Canonical(new Kmer(5)));
    }

    /**
     * @test
     */
    public function max() : void
    {
        $this->assertEquals(34, $this->hashTable->max());
    }

    /**
     * @test
     */
    public function argmax() : void
    {
        $this->assertEquals('AACAT', $this->hashTable->argmax());
    }

    /**
     * @test
     */
    public function numSingletons() : void
    {
        $this->assertEquals(0, $this->hashTable->numSingletons());
    }

    /**
     * @test
     */
    public function totalSequences() : void
    {
        $this->assertEquals(self::NUM_SEQUENCES, $this->hashTable->totalSequences());
    }

    /**
     * @test
     */
    public function top() : void
    {
        $expected = [
            'AACAT' => 34,
            'CGACC' => 33,
            'AATTA' => 32,
        ];

        $this->assertEquals($expected, $this->hashTable->top(3));
    }

    /**
     * @test
     */
    public function histogram() : void
    {
        $expected = [
            7 => 0,
            14 => 65,
            21 => 344,
            28 => 495,
            34 => 512,
        ];

        $this->assertEquals($expected, $this->hashTable->histogram(5));
    }

    /**
     * @test
     */
    public function offsetSet() : void
    {
        $this->hashTable['GAATA'] = 42;

        $this->assertEquals(42, $this->hashTable['GAATA']);
    }

    /**
     * @test
     */
    public function offsetExists() : void
    {
        $this->assertTrue(isset($this->hashTable['AATTA']));

        $this->assertFalse(isset($this->hashTable['ACTNG']));
    }

    /**
     * @test
     */
    public function offsetGet() : void
    {
        $this->assertEquals(32, $this->hashTable['AATTA']);
    }

    /**
     * @test
     */
    public function countable() : void
    {
        $this->assertEquals(512, count($this->hashTable));
    }
}
