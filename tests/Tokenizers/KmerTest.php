<?php

namespace DNATools\Tests\Tokenizers;

use DNATools\Tokenizers\Kmer;
use PHPUnit\Framework\TestCase;

/**
 * @group Tokenizers
 * @covers \DNATools\Tokenizers\Kmer
 */
class KmerTest extends TestCase
{
    /**
     * @var \DNATools\Tokenizers\Kmer
     */
    protected $tokenizer;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->tokenizer = new Kmer(6, ['CGGTTCAGCANG']);
    }

    /**
     * @test
     */
    public function tokenize() : void
    {
        $expected = ['CGGTTC', 'GGTTCA', 'GTTCAG', 'TTCAGC', 'TCAGCA'];

        $this->assertEquals($expected, iterator_to_array($this->tokenizer));
    }
}
