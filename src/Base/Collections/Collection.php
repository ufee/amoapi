<?php
/**
 * Amoapi Collection class
 * @author Vlad Ionov <vlad@f5.com.ru>
 */
namespace Ufee\Amo\Base\Collections;

class Collection implements \IteratorAggregate 
{
	protected $items;
	
    /**
     * Constructor
	 * @param array $elements
     */
    public function __construct(Array $elements = [])
    {
        $this->items = $elements;
	}
	
    /**
     * Collection iterator
	 * @return \ArrayIterator
     */
	public function getIterator(): \Traversable
	{
		return new \ArrayIterator($this->items);
	}
	
    /**
     * Get count elements
	 * @return integer
     */
    public function count()
    {
		return count($this->items);
	}
	
    /**
     * Get all elements
	 * @return array
     */
    public function all()
    {
		return $this->items;
	}

    /**
     * Get unique elements collection
	 * @param integer $flags
	 * @return Collection
     */
    public function unique($flags = SORT_STRING)
    {
		return new static(
			array_unique($this->items, $flags)
		);
	}
	
    /**
     * Push new elements
	 * @param mixed $element
	 * @return Collection
     */
    public function push($element)
    {
		array_push($this->items, $element);
		return $this;
	}
	
    /**
     * Merge collections
	 * @param Collection $collection
	 * @return Collection
     */
    public function merge(Collection &$collection)
    {
		$this->items = array_merge($this->items, $collection->all());
		return $this;
	}
	
    /**
     * Chunk collection
	 * @param integer $size
	 * @param bool $preserve_keys
	 * @return Collection
     */
    public function chunk($size, $preserve_keys = false)
    {
		$this->items = array_chunk($this->items, $size, $preserve_keys);
		return $this;
	}
	
    /**
     * Sort elements
	 * @param callable $callback
	 * @return Collection
     */
    public function usort(callable $callback)
    {
		usort($this->items, $callback);
		return $this;
	}
	
    /**
     * Sort elements
	 * @param callable $callback
	 * @return Collection
     */
    public function uasort(callable $callback)
    {
		uasort($this->items, $callback);
		return $this;
	}
	
    /**
     * Sort collection by key
	 * @param string $key items element key
	 * @param string $type sort type
	 * @return Collection
     */
    public function sortBy($key, $type = 'ASC')
    {
		$sort_keys = (strtoupper($type) == 'DESC') ? [1, -1] : [-1, 1];
		$first = $this->first();
		if (is_object($first)) {
			return $this->_objSortBy($key, $sort_keys);
		}
		if (is_array($first)) {
			return $this->_arrSortBy($key, $sort_keys);
		}
		return $this;
	}
	
    /**
     * Sort object collection by key
	 * @param string $key items element key
	 * @param array $sort_keys
	 * @return Collection
     */
    protected function _objSortBy($key, $sort_keys)
    {
		return $this->usort(function($a, $b) use ($key, $sort_keys) {
			if ($a->{$key} == $b->{$key}) {
				return 0;
			}
			return ($a->{$key} < $b->{$key}) ? $sort_keys[0] : $sort_keys[1];
		});
	}
	
    /**
     * Sort array collection by key
	 * @param string $key items element key
	 * @param array $sort_keys
	 * @return Collection
     */
    protected function _arrSortBy($key, $sort_keys)
    {
		return $this->usort(function($a, $b) use ($key, $sort_keys) {
			if ($a[$key] == $b[$key]) {
				return 0;
			}
			return ($a[$key] < $b[$key]) ? $sort_keys[0] : $sort_keys[1];
		});
	}
	
    /**
     * Sort simple collection
	 * @param string $type sort type
	 * @return Collection
     */
    public function sort($type = 'ASC')
    {
		$sort_keys = ($type == 'DESC') ? [1, -1] : [-1, 1];
		return $this->usort(function($a, $b) use ($sort_keys) {
			if ($a == $b) {
				return 0;
			}
			return ($a < $b) ? $sort_keys[0] : $sort_keys[1];
		});
	}
	
    /**
     * Each elements
	 * @param callable $callback (item, key)
	 * @return Collection
     */
    public function each(callable $callback)
    {
		array_walk($this->items, $callback);
		return $this;
	}

    /**
     * Transform elements
	 * @param callable $callback (item, key)
	 * @return Collection
     */
    public function transform(callable $callback)
    {
		foreach ($this->items as $key=>&$item) {
			$item = $callback($item, $key);
		}
		return $this;
	}
	
    /**
     * Join elements
	 * @param string $delimiter
	 * @return string
     */
    public function join($delimiter = '')
    {
		return join($delimiter, $this->items);
	}
	
    /**
     * Sum elements
	 * @param string $key
	 * @return integer|double
     */
    public function sum($key = null)
    {
		$sum = 0;
		if (is_null($key)) {
			return array_sum($this->items);
		}
		if (is_object($this->first())) {
			foreach	($this->items as $item) {
				$sum+= (float)$item->{$key};
			}
		} else if (is_array($this->first())) {
			foreach	($this->items as $item) {
				$sum+= (float)$item[$key];
			}
		}
		return $sum;
	}
	
    /**
     * Filter elements
	 * @param callable $callback ($item, $key)
	 * @return array
     */
    public function filter($callback)
    {
        if (!is_callable($callback)) {
			throw new \Exception('Invalid argument callback function');
		}
		return new static(
			array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH)
		);
	}
	
    /**
     * Map elements
	 * @param callable $callback ($item, $key)
	 * @return array
     */
    public function map($callback)
    {
        if (!is_callable($callback)) {
			throw new \Exception('Invalid argument callback function');
		}
		return new static(array_map($callback, $this->items));
	}
	
    /**
     * Has elements value
	 * @param string $a items element value || key
	 * @param string $b items element value
	 * @return mixed
     */
    public function contains($a, $b = null)
    {
		if (is_null($b)) {
			if (!is_array($a)) {
				$a = [$a];
			}
			return $this->_containsArr($a);
		}
		if (is_object($this->first())) {
			return $this->_containsObj($a, $b);
		}
		return false;
	}
	
    /**
     * Has elements value - one array
	 * @param array $vals element values
	 * @return mixed
     */
    protected function _containsArr($vals)
    {
		foreach ($vals as $val) {
			if (!in_array($val, $this->items)) {
				return false;
			}
		}
		return true;
	}
	
    /**
     * Has elements value - objects
	 * @param mixed $key element key
	 * @param mixed $val element value
	 * @return mixed
     */
    protected function _containsObj($key, $val)
    {
		foreach ($this->items as $item) {
			if ($val == $item->$key) {
				return true;
			}
		}
		return false;
	}
	
    /**
     * Get elements by value
	 * @param mixed $a items element value || key
	 * @param string $b items element value
	 * @return Collection
     */
    public function find($a, $b = null)
    {
		if (is_callable($a) && is_null($b)) {
			return $this->_findCallable($a);
		}
		if (is_null($b)) {
			if (is_callable($a)) {
				return $this->_findCallable($a);
			}
			if (!is_array($a)) {
				$a = [$a];
			}
			return $this->_findArr($a);
		}
		if (is_object($this->first())) {
			return $this->_findObj($a, $b);
		}
		if (is_array($this->first())) {
			return $this->_findArrKey($a, $b);
		}
		return new static();
	}
	
    /**
     * Get elements by value - one array
	 * @param array $vals items element value
	 * @return Collection
     */
    protected function _findArr($vals)
    {
		$findedVals = [];
		foreach ($this->items as $item) {
			foreach ($vals as $val) {
				if ($val == $item) {
					$findedVals[]= $item;
				}
			}
		}
		return new static($findedVals);
	}
	
    /**
     * Get elements by value - array
	 * @param string $key items element key
	 * @param mixed $val items element value
	 * @return Collection
     */
    protected function _findArrKey($key, $val)
    {
		$findedByKey = [];
		foreach ($this->items as $item) {
			if ($val == $item[$key]) {
				$findedByKey[]= $item;
			}
		}
		return new static($findedByKey);
	}
	
    /**
     * Get elements by value - objects
	 * @param string $key items element key
	 * @param mixed $val items element value
	 * @return Collection
     */
    protected function _findObj($key, $val)
    {
		$findedFromObj = [];
		foreach ($this->items as $item) {
			if (get_class($item) == 'stdClass' && !property_exists($item, $key)) {
				continue;
			}
			if ($val == $item->{$key}) {
				$findedFromObj[]= $item;
			}
		}
		return new static($findedFromObj);
	}

    /**
     * Get elements by callback
	 * @param callable $callback
	 * @return Collection
     */
    protected function _findCallable($callback)
    {
		$finded = [];
		foreach ($this->items as $item) {
			if ($callback($item)) {
				$finded[]= $item;
			}
		}
		return new static($finded);
	}
	
    /**
     * Group collection by key
	 * @param mixed $key items element key
	 * @return Collection
     */
    public function groupBy($key)
    {
		$grouped_items = [];
		foreach ($this->items as $item) {
			if (is_object($item)) {
				$group_value = $item->{$key};
			} else {
				$group_value = $item[$key];
			}
			if (!isset($grouped_items[$group_value])) {
				$grouped_items[$group_value] = [];
			}
			$grouped_items[$group_value][]= $item;
		}
		return new static($grouped_items);
	}
	
    /**
     * Slice collection 
	 * @param integer $offset
	 * @param integer $length
	 * @return Collection
     */
    public function slice($offset, $length)
    {
		$this->items = array_slice($this->items, $offset, $length);
		return $this;
	}
	
    /**
     * Get element
	 * @param mixed $key items element key
	 * @return mixed
     */
    public function get($key)
    {
		if (!array_key_exists($key, $this->items)) {
			return null;
		}
		return $this->items[$key];
	}
	
    /**
     * Has iset keys
	 * @param mixed $keys items element key
	 * @return mixed
     */
    public function has($keys)
    {
		if (!is_array($keys)) {
			$keys = [$keys];
		}
		foreach ($keys as $key) {
			if (!array_key_exists($key, $this->items)) {
				return false;
			}
		}
		return true;
	}
	
    /**
     * Get first elem
	 * @return mixed
     */
    public function first()
    {
		return reset($this->items);
	}
	
    /**
     * Get last elem
	 * @return mixed
     */
    public function last()
    {
		return end($this->items);
	}
	
    /**
     * Get end elem
	 * @return mixed
     */
    public function end()
    {
		return $this->last();
	}
	
    /**
     * Unset item by key
	 * @return Collection
     */
    public function remove($key)
    {
		if ($this->has($key)) {
			unset($this->items[$key]);
		}
		return $this;
	}

    /**
     * Get array data from collection
	 * @return array
     */
    public function toArray()
    {
		$items = [];
		$this->each(function($item, $key) use(&$items) {
			if (is_object($item) && method_exists($item, 'toArray')) {
				$items[$key]= $item->toArray();
			} else {
				$items[$key]= $item;
			}
		});
		return $items;
	}
}
