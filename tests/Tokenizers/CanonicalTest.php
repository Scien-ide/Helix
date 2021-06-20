<?php

namespace DNAToolkit\Tests\Extractors;

use DNAToolkit\Tokenizers\Canonical;
use DNAToolkit\Tokenizers\Kmer;
use PHPUnit\Framework\TestCase;

/**
 * @group Extractors
 * @covers \DNAToolkit\Tokenizers\Canonical
 */
class CanonicalTest extends TestCase
{
    /**
     * @var \DNAToolkit\Tokenizers\Canonical
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
