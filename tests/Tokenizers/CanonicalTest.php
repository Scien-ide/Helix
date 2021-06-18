<?php

namespace DNAHash\Tests\Extractors;

use DNAHash\Tokenizers\Canonical;
use DNAHash\Tokenizers\Kmer;
use PHPUnit\Framework\TestCase;

/**
 * @group Extractors
 * @covers \DNAHash\Tokenizers\Canonical
 */
class CanonicalTest extends TestCase
{
    /**
     * @var \DNAHash\Tokenizers\Canonical
     */
    protected $extractor;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->extractor = new Canonical(new Kmer(6, [
            'CGGTTCAGCANG',
        ]));
    }

    /**
     * @test
     */
    public function tokenize() : void
    {
        $expected = ['CGGTTC', 'GGTTCA', 'CTGAAC', 'GCTGAA', 'TCAGCA'];

        $this->assertEquals($expected, iterator_to_array($this->extractor));
    }
}
