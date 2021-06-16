<?php

namespace DNAHash\Tests\Extractors;

use DNAHash\Extractors\Canonical;
use DNAHash\Extractors\Kmer;
use PHPUnit\Framework\TestCase;

/**
 * @group Extractors
 * @covers \DNAHash\Extractors\Canonical
 */
class CanonicalTest extends TestCase
{
    /**
     * @var \DNAHash\Extractors\Canonical
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
