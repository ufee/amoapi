<?php

namespace Ufee\Amo\Base\Models\Interfaces;

interface LinkedTags
{
    /**
     * Attach tags
     * @param array $tags
     * @return static
     */
    public function attachTags(Array $tags);

    /**
     * Attach tag
     * @param string $tag
     * @return static
     */
    public function attachTag($tag);

    /**
     * Has isset tags
     * @param array $tags
     * @return bool
     */
    public function hasTags(Array $tags);

    /**
     * Has isset tag
     * @param string $tag
     * @return bool
     */
    public function hasTag($tag);

    /**
     * Detach tags
     * @param mixed $tags
     * @return static
     */
    public function detachTags($tags = null);

    /**
     * Detach tag
     * @param string $tag
     * @return static
     */
    public function detachTag($tag);

}