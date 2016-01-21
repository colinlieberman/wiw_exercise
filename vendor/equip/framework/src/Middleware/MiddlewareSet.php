<?php

namespace Equip\Middleware;

use Destrukt\Set;

class MiddlewareSet extends Set
{
    /**
     * @param array $middlewares
     * @throws \DomainException if $middlewares does not conform to type expectations
     */
    public function validate(array $middlewares)
    {
        parent::validate($middlewares);

        foreach ($middlewares as $middleware) {
            if (!(is_callable($middleware) || method_exists($middleware, '__invoke'))) {
                throw new \DomainException(
                    'All elements of $middlewares must be callable'
                );
            }
        }
    }
}
