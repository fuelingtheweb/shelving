<?php namespace Fuelingtheweb\Shelving;

trait OwnerTrait {

    /**
     * Owner Id
     * @var null
     */
    protected $ownerId = null;

    /**
     * Add owner id to instance on insert
     * @param $instance
     */
    protected function addOwnerOnInsert($instance) {
        if (!is_null($this->ownerId)) {
            $instance->setAttribute('owner_id', $this->ownerId);
        }

        return $instance;
    }

}
