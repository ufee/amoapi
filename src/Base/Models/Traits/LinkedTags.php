<?php
/**
 * amoCRM Base trait - linked tags
 */
namespace Ufee\Amo\Base\Models\Traits;

trait LinkedTags
{
    /**
     * Attach tags
	 * @param array $tags
     * @return static
     */
    public function attachTags(Array $tags)
    {
		foreach ($tags as $tag) {
			$this->attachTag($tag);
		}
		return $this;
	}
	
    /**
     * Attach tag
	 * @param string $tag
     * @return static
     */
    public function attachTag($tag)
    {
		if (!$this->hasTag($tag, $this->tags)) {
			$this->attributes['tags'][]= $tag;
			$this->changed[]= 'tags';
		}
		return $this;
	}
	
    /**
     * Has isset tags
	 * @param array $tags
     * @return bool
     */
    public function hasTags(Array $tags)
    {
		foreach ($tags as $tag) {
			if (!$this->hasTag($tag)) {
				return false;
			}
		}
		return true;
	}
	
    /**
     * Has isset tag
	 * @param string $tag
     * @return bool
     */
    public function hasTag($tag)
    {
		return in_array($tag, $this->tags);
	}
	
    /**
     * Detach tags
	 * @param mixed $tags
     * @return static
     */
    public function detachTags($tags = null)
    {
		if (is_null($tags) && !empty($this->attributes['tags'])) {
			$this->attributes['tags'] = [];
			$this->changed[]= 'tags';
		} else if(is_array($tags)) {
			foreach ($tags as $tag) {
				$this->detachTag($tag);
			}
		}
		return $this;
	}
	
    /**
     * Detach tag
	 * @param string $tag
     * @return static
     */
    public function detachTag($tag)
    {
		$key = array_search($tag, $this->attributes['tags']);
		if ($key !== false) {
			unset($this->attributes['tags'][$key]);
			$this->changed[]= 'tags';
		}
		return $this;
	}
}
