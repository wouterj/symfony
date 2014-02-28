<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Controller;

/**
 * An ArgumentResolverInterface implementation resolves the arguments of 
 * controllers.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
interface ArgumentResolverInterface
{
    /**
     * Checks if the current parameter can be resolved by this argument 
     * resolver.
     *
     * @param \ReflectionParameter $parameter
     *
     * @return Boolean
     */
    public function supports(\ReflectionParameter $parameter);

    /**
     * Resolves the current parameter into an argument.
     *
     * @param \ReflectionParameter $parameter
     * 
     * @return mixed The resolved argument
     */
    public function resolve(\ReflectionParameter $parameter);
}
