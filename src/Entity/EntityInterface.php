<?php

namespace App\Entity;

/**
 * Entity Interface.
 *
 * @category Entity
 */
interface EntityInterface
{
    /**
     * Return the id or null if entity was never saved.
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Return the label of entity.
     *
     * @return string|null
     */
    public function getLabel(): ?string;
}
