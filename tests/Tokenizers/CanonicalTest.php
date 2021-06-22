<?php

namespace DNATools\Tests\Tokenizers;

use DNATools\Tokenizers\Canonical;
use DNATools\Tokenizers\Kmer;
use PHPUnit\Framework\TestCase;

/**
 * @group Tokenizers
 * @covers \DNATools\Tokenizers\Canonical
 */
class CanonicalTest extends TestCase
{
    /**
     * @var \DNATools\Tokenizers\Canonical
     */
    protected $tokenizer;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->tokenizer = new Canonical(new Kmer(6));
    }

    /**
     * @test
     */
    public function tokenize() : void
    {
        $expected = ['CGGTTC', 'GGTTCA', 'CTGAAC', 'GCTGAA', 'TCAGCA'];

        $tokens = $this->tokenizer->tokenize('CGGTTCAGCANG');

        $this->assertEquals($expected, iterator_to_array($tokens));
    }
}
