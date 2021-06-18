# DNA Hash
A PHP hash table optimized for counting short gene sequences for use in Bioinformatics. DNA Hash stores sequence counts by their up2bit encoding - a two-way hash that exploits the fact that each DNA base need only 2 bits to be fully encoded. Accordingly, DNA Hash uses less memory than a lookup table that stores raw gene sequences. In addition, DNA Hash's layered Bloom filter eliminates the need to explicitly store counts for sequences that have only been seen once.

- **Ultra-low** memory footprint
- **Compatible** with multiple dataset formats
- **Supports** canonical sequence counting
- **Open-source** and free to use commercially

> **Note:** The maximum sequence length is platform dependent. On a 64-bit machine, the max length is 31. On a 32-bit machine, the max length is 15.

> **Note:** Due to the probabilistic nature of the Bloom filter, DNA Hash may under count unique sequences at a bounded rate.

## Installation
Install into your project using [Composer](https://getcomposer.org/):

```sh
$ composer require scienide/dnahash
```

### Requirements
- [PHP](https://php.net/manual/en/install.php) 7.4 or above

## Example Usage

```php
use DNAHash\DNAHash;
use DNAHash\Extractors\FASTA;
use DNAHash\Tokenizers\Canonical;
use DNAHash\Tokenizers\Kmer;

$extractor = new FASTA('example.fa');

$hashTable = new DNAHash(0.001);

$hashTable->import(new Canonical(new Kmer(25, $extractor)));

$top10 = $hashTable->top(10);

print_r($top10);
```

```
Array
(
    [GCTATAAAAAGAAAATTTTGGAATA] => 19
    [ATTCCAAAATTTTCTTTTTATAGCC] => 19
    [TAAAAAGAAAATTTTGGAATAAAAA] => 18
    [ATAAAAAGAAAATTTTGGAATAAAA] => 18
    [TATAAAAAGAAAATTTTGGAATAAA] => 18
    [CTATAAAAAGAAAATTTTGGAATAA] => 18
    [AAATAATTTCAATTTTCTATCTCAA] => 17
    [AAAATAATTTCAATTTTCTATCTCA] => 17
    [CAAAATAATTTCAATTTTCTATCTC] => 17
    [AGATAGAAAATTGAAATTATTTTGA] => 17
)
```

```php
echo $hashTable->falsePositiveRate();
```

```
0.0003865
```

## Testing
To run the unit tests:

```sh
$ composer test
```
## Static Analysis
To run static code analysis:

```sh
$ composer analyze
```

## Benchmarks
To run the benchmarks:

```sh
$ composer benchmark
```

## References
- [1] https://github.com/JohnLonginotto/ACGTrie/blob/master/docs/UP2BIT.md.
- [2] P. Melsted et al. (2011). Efficient counting of k-mers in DNA sequences using a bloom filter.
- [3] S. Deorowicz1 et al. (2015). KMC 2: fast and resource-frugal k-mer counting.
