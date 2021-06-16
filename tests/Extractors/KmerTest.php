<?php

namespace DNAHash\Tests\Extractors;

use DNAHash\Extractors\Kmer;
use PHPUnit\Framework\TestCase;

/**
 * @group Extractors
 * @covers \DNAHash\Extractors\Kmer
 */
class KmerTest extends TestCase
{
    /**
     * @var \DNAHash\Extractors\Kmer
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
