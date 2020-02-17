<?php

namespace App\Entity\Traits;

/**
 * Slug trait.
 *
 * @category Trait
 *
 * @author   David Romaní <david@flux.cat>
 */
trait SlugTrait
{
    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
