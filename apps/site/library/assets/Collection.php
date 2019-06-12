<?php

namespace apps\site\library\assets;

/**
 * Phalcon\Assets\Collection
 *
 * Represents a collection of resources
 */
class Collection extends \Phalcon\Assets\Collection
{
    protected $assets_collections_expiration_token;

    public function __construct($assets_collections_expiration_token)
    {
        $this->assets_collections_expiration_token = $assets_collections_expiration_token;
    }

    /**
     * Sets a target uri for the generated HTML
     *
     * @param string $targetUri
     * @return Collection
     */
    public function setTargetUri($targetUri)
    {
        if (!empty($this->assets_collections_expiration_token)) {
            $targetUri .= '?v=' . $this->assets_collections_expiration_token;
        }
        return parent::setTargetUri($targetUri);

    }

}
