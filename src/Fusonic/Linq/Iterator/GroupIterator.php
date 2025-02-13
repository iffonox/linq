<?php

/*
 * This file is part of Fusonic-linq.
 * https://github.com/fusonic/fusonic-linq
 *
 * (c) Fusonic GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fusonic\Linq\Iterator;

use Iterator;
use Fusonic\Linq\GroupedLinq;

class GroupIterator implements Iterator
{
    private $iterator;
    private $grouped;
    private $keySelector;

    public function __construct($iterator, callable $keySelector)
    {
        $this->iterator = $iterator;
        $this->keySelector = $keySelector;
    }

    public function current(): GroupedLinq
    {
		if ($this->grouped === null) {
			$this->doGroup();
		}
		
        $current = $this->grouped->current();
        return new GroupedLinq($current['key'], new \ArrayIterator($current['values']));
    }

    public function next(): void
    {
		if ($this->grouped === null) {
			$this->doGroup();
		}
		
        $this->grouped->next();
    }
	
	#[\ReturnTypeWillChange]
    public function key()
    {
		if ($this->grouped === null) {
			$this->doGroup();
		}
		
        return $this->grouped->key();
    }

    public function valid(): bool
    {
		if ($this->grouped === null) {
			$this->doGroup();
		}
		
        return $this->grouped->valid();
    }

    public function rewind(): void
    {
        if ($this->grouped === null) {
            $this->doGroup();
        }

        $this->grouped->rewind();
    }

    private function doGroup()
    {
        $keySelector = $this->keySelector;
        $this->grouped = new \ArrayIterator([]);
        foreach ($this->iterator as $value) {
            $key = $keySelector($value);
            if (!isset($this->grouped[$key])) {
                $this->grouped[$key] = ['key' => $key, 'values'=> []];
            }

            $this->grouped[$key]['values'][] = $value;
        }
    }
}
