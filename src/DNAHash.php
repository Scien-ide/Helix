<?php

namespace DNAHash;

use OkBloomer\BloomFilter;
use DNAHash\Exceptions\InvalidArgumentException;
use DNAHash\Exceptions\RuntimeException;
use DNAHash\Exceptions\SequenceTooLong;
use DNAHash\Exceptions\InvalidBase;
use ArrayAccess;
use Countable;

use function count;
use function is_int;
use function arsort;
use function array_slice;
use function strlen;
use function log;
use function max;

/**
 * DNA Hash
 *
 * A hash table optimized for counting short DNA sequences called k-mers. This implementation uses the
 * up2bit format to efficiently store DNA sequences as scalar integers - effectively acting as a 2-way
 * hash function.
 *
 * References:
 * [1] https://github.com/JohnLonginotto/ACGTrie/blob/master/docs/UP2BIT.md
 * [2] P. Melsted et al. (2011). Efficient counting of k-mers in DNA sequences using a bloom filter.
 * [3] S. Deorowicz1 et al. (2015). KMC 2: fast and resource-frugal k-mer counting.
 *
 * @category    Bioinformatics
 * @package     andrewdalpino/DNAHash
 * @author      Andrew DalPino
 *
 * @implements ArrayAccess<string, int>
 */
class DNAHash implements ArrayAccess, Countable
{
    /**
     * The platform-dependent maximum size of a sequence.
     *
     * @var int
     */
    protected const MAX_SEQUENCE_LENGTH = (4 * PHP_INT_SIZE) - 1;

    /**
     * A single bit added to every 2bit sequence.
     *
     * @var int
     */
    protected const UP_BIT = 1;

    /**
     * The mapping of bases to their 2-bit encoding.
     *
     * @var int[]
     */
    protected const BASE_ENCODE_MAP = [
        'A' => 0,
        'C' => 1,
        'T' => 2,
        'G' => 3,
    ];

    /**
     * The mapping of 2-bit encodings to their bases.
     *
     * @var string[]
     */
    protected const BASE_DECODE_MAP = [
        0 => 'A',
        1 => 'C',
        2 => 'T',
        3 => 'G',
    ];

    /**
     * A Bloom filter containing sequences that have probably been seen at least once.
     *
     * @var \OkBloomer\BloomFilter
     */
    protected \OkBloomer\BloomFilter $filter;

    /**
     * A sequential array of sequence counts indexed by their up2bit encoding.
     *
     * @var int[]
     */
    protected array $counts = [
        //
    ];

    /**
     * The number of single sequences in the hash table.
     *
     * @var int
     */
    protected int $numSingletons = 0;

    /**
     * The total number of non-singleton sequences in the hash table.
     *
     * @var int
     */
    protected int $numNonSingletons = 0;

    /**
     * Encode a sequence as an integer using the up2bit format.
     *
     * @param string $sequence
     * @throws \DNAHash\Exceptions\SequenceTooLong
     * @throws \DNAHash\Exceptions\InvalidBase
     * @return int
     */
    protected static function encode(string $sequence) : int
    {
        $k = strlen($sequence);

        if ($k > self::MAX_SEQUENCE_LENGTH) {
            throw new SequenceTooLong($k, self::MAX_SEQUENCE_LENGTH);
        }

        $hash = self::UP_BIT;

        for ($i = $k - 1; $i >= 0; --$i) {
            $base = $sequence[$i];

            if (!isset(self::BASE_ENCODE_MAP[$base])) {
                throw new InvalidBase($base, self::BASE_DECODE_MAP);
            }

            $hash = ($hash << 2) + self::BASE_ENCODE_MAP[$base];
        }

        return $hash;
    }

    /**
     * Decode a sequence from an up2bit representation.
     *
     * @param int $hash
     * @return string
     */
    protected static function decode(int $hash) : string
    {
        $k = (int) log($hash, 2);

        $sequence = '';

        for ($i = 0; $i < $k; $i += 2) {
            $base = ($hash >> $i) & 3;

            $sequence .= self::BASE_DECODE_MAP[$base];
        }

        return $sequence;
    }

    /**
     * @param float $maxFalsePositiveRate
     * @param int|null $numHashes
     * @param int $layerSize
     */
    public function __construct(
        float $maxFalsePositiveRate = 0.01,
        ?int $numHashes = 4,
        int $layerSize = 32000000
    ) {
        $this->filter = new BloomFilter($maxFalsePositiveRate, $numHashes, $layerSize);
    }

    /**
     * Import a sequence dataset into the hash table.
     *
     * @param iterable<string> $iterator
     * @return self
     */
    public function import(iterable $iterator) : self
    {
        foreach ($iterator as $sequence) {
            $this->increment($sequence);
        }

        return $this;
    }

    /**
     * Increment the count for a given sequence by 1.
     *
     * @param string $sequence
     */
    public function increment(string $sequence) : void
    {
        $exists = $this->filter->existsOrInsert($sequence);

        if ($exists) {
            $hash = self::encode($sequence);

            if (isset($this->counts[$hash])) {
                ++$this->counts[$hash];

                ++$this->numNonSingletons;
            } else {
                --$this->numSingletons;

                $this->counts[$hash] = 2;

                $this->numNonSingletons += 2;
            }
        } else {
            ++$this->numSingletons;
        }
    }

    /**
     * Return the underlying Bloom filter.
     *
     * @return \OkBloomer\BloomFilter
     */
    public function filter() : BloomFilter
    {
        return $this->filter;
    }

    /**
     * Return the number of sequences that are only counted once.
     *
     * @return int
     */
    public function numSingletons() : int
    {
        return $this->numSingletons;
    }

    /**
     * Return the total number of non-singleton sequences in the hash table.
     *
     * @return int
     */
    public function numNonSingletons() : int
    {
        return $this->numNonSingletons;
    }

    /**
     * Return the total number of sequences counted so far.
     *
     * @return int
     */
    public function totalSequences() : int
    {
        return $this->numNonSingletons + $this->numSingletons;
    }

    /**
     * Return the highest sequence count.
     *
     * @return int
     */
    public function max() : int
    {
        return (int) max($this->counts);
    }

    /**
     * Return the sequence with the highest count.
     *
     * @return string|null
     */
    public function argmax() : ?string
    {
        $hash = argmax($this->counts);

        if (!is_int($hash)) {
            return null;
        }

        $sequence = self::decode($hash);

        return $sequence;
    }

    /**
     * Return the k sequences with the highest counts.
     *
     * @param int $k
     * @throws \DNAHash\Exceptions\InvalidArgumentException
     * @return int[]
     */
    public function top(int $k = 10) : array
    {
        if ($k < 1) {
            throw new InvalidArgumentException('K must be'
                . " greater than 1, $k given.");
        }

        arsort($this->counts);

        $counts = array_slice($this->counts, 0, $k, true);

        $results = [];

        foreach ($counts as $hash => $count) {
            $sequence = self::decode($hash);

            $results[$sequence] = $count;
        }

        return $results;
    }

    /**
     * Return a histogram of sequences bucketed by their counts.
     *
     * @param int $bins
     * @throws \DNAHash\Exceptions\InvalidArgumentException
     * @throws \DNAHash\Exceptions\RuntimeException
     * @return int[]
     */
    public function histogram(int $bins = 10) : array
    {
        if ($bins < 2) {
            throw new InvalidArgumentException('Number of bins'
                . " must be greater than 2, $bins given.");
        }

        if ($this->totalSequences() === 0) {
            throw new RuntimeException('At least one element'
                . ' is needed to generate a histogram.');
        }

        if (empty($this->counts)) {
            $max = 1;
        } else {
            $max = (int) max($this->counts);
        }

        $interval = (int) ceil($max / $bins);

        $edges = [$interval];

        while (count($edges) < $bins) {
            $edges[] = end($edges) + $interval;
        }

        $histogram = array_fill_keys($edges, 0);

        foreach ($this->counts as $count) {
            foreach ($edges as $edge) {
                if ($count <= $edge) {
                    ++$histogram[$edge];

                    break;
                }
            }
        }

        $histogram[reset($edges)] += $this->numSingletons;

        return $histogram;
    }

    /**
     * Set the count for a given sequence.
     *
     * @param string $sequence
     * @param int $count
     * @throws \DNAHash\Exceptions\InvalidArgumentException
     */
    public function offsetSet($sequence, $count) : void
    {
        if ($count < 1) {
            throw new InvalidArgumentException('Count must be'
                . " greater than 1, $count given.");
        }

        $exists = $this->filter->existsOrInsert($sequence);

        $hash = self::encode($sequence);

        if (isset($this->counts[$hash])) {
            $this->numNonSingletons -= $this->counts[$hash];
        } elseif ($exists) {
            --$this->numSingletons;
        }

        if ($count > 1) {
            $this->counts[$hash] = $count;

            $this->numNonSingletons += $count;
        } else {
            ++$this->numSingletons;
        }
    }

    /**
     * Does a given sequence exist in the hash table?
     *
     * @param string $sequence
     * @return bool
     */
    public function offsetExists($sequence) : bool
    {
        return $this->filter->exists($sequence);
    }

    /**
     * Return the count for a given sequence.
     *
     * @param string $sequence
     * @throws \DNAHash\Exceptions\InvalidArgumentException
     * @return int
     */
    public function offsetGet($sequence) : int
    {
        if (!$this->offsetExists($sequence)) {
            throw new InvalidArgumentException("Sequence '$sequence' not found.");
        }

        $hash = self::encode($sequence);

        if (isset($this->counts[$hash])) {
            return $this->counts[$hash];
        }

        return 1;
    }

    /**
     * @param string $sequence
     * @throws \DNAHash\Exceptions\RuntimeException
     */
    public function offsetUnset($sequence) : void
    {
        throw new RuntimeException('Unset not supported.');
    }

    /**
     * Return the number of unique sequences stored in the hash table.
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->counts) + $this->numSingletons;
    }
}
