<?php

namespace DNATools\Tests\Tokenizers;

use DNATools\Tokenizers\Kmer;
use DNATools\Tokenizers\Unique;
use PHPUnit\Framework\TestCase;

/**
 * @group Tokenizers
 * @covers \Rubix\ML\Tokenizers\Unique
 */
class UniqueTest extends TestCase
{
    /**
     * @var \DNATools\Tokenizers\Unique
     */
    protected $tokenizer;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->tokenizer = new Unique(new Kmer(3, ['AAAAAAATCGCTCGC']));
    }

    /**
     * @test
     */
    public function tokenize() : void
    {
        $expected = ['AAA', 'AAT', 'ATC', 'TCG', 'CGC', 'GCT', 'CTC'];

        $this->assertEquals($expected, iterator_to_array($this->tokenizer));
    }
}
