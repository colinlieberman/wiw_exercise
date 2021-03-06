<?php

namespace Destrukt;

interface StructInterface extends
    \ArrayAccess,
    \Countable,
    \Iterator,
    \JsonSerializable,
    \Serializable
{
    /**
     * Check if given structure is the same as this structure.
     *
     * @param  StructInterface $target
     * @return boolean
     */
    public function isSimilar(StructInterface $target);

    /**
     * Get an array copy of the current structure.
     *
     * @return array
     */
    public function toArray();

    /**
     * Validate an array for correct structure.
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    public function validate(array $data);
}
