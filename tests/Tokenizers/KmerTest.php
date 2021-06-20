<?php

namespace DNAToolkit\Tests\Extractors;

use DNAToolkit\Tokenizers\Kmer;
use PHPUnit\Framework\TestCase;

/**
 * @group Extractors
 * @covers \DNAToolkit\Tokenizers\Kmer
 */
class KmerTest extends TestCase
{
    /**
     * @var \DNAToolkit\Tokenizers\Kmer
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
