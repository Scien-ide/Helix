<?php

namespace DNAHash\Tests\Extractors;

use DNAHash\Tokenizers\Kmer;
use PHPUnit\Framework\TestCase;

/**
 * @group Extractors
 * @covers \DNAHash\Tokenizers\Kmer
 */
class KmerTest extends TestCase
{
    /**
     * @var \DNAHash\Tokenizers\Kmer
     */
    protected $extractor;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->extractor = new Kmer(6, ['CGGTTCAGCANG']);
    }

    /**
     * @test
     */
    public function tokenize() : void
    {
        $expected = ['CGGTTC', 'GGTTCA', 'GTTCAG', 'TTCAGC', 'TCAGCA'];

        $this->assertEquals($expected, iterator_to_array($this->extractor));
    }
}
