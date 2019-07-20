<?php

namespace Symfony\Component\Security\Principal\Token;

use function foo\func;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 * @internal
 * @deprecated since Symfony 4.4, to be removed in Symfony 5.0.
 */
trait DeprecatedTokenMethodsTrait
{
    /** @deprecated since Symfony 4.4, to be removed in Symfony 5.0. */
    public function getUsername()
    {
        @trigger_error('Method '.__METHOD__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        return parent::getUsername();
    }

    /** @deprecated since Symfony 4.4, to be removed in Symfony 5.0. */
    public function getUser()
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        return parent::getUser();
    }

    /** @deprecated since Symfony 4.4, to be removed in Symfony 5.0. */
    public function setUser($user/*, $hideDeprecation = false*/)
    {
        if (1 === func_num_args() || false === func_get_arg(1)) {
            @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);
        }

        parent::setUser($user);
    }

    /** @deprecated since Symfony 4.4, to be removed in Symfony 5.0. */
    public function isAuthenticated()
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0. Check instance of '.__NAMESPACE__.'\\AuthenticatedTokenInterface instead.', E_USER_DEPRECATED);

        return parent::isAuthenticated();
    }

    /** @deprecated since Symfony 4.4, to be removed in Symfony 5.0. */
    public function setAuthenticated($authenticated)
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        parent::setAuthenticated($authenticated);
    }

    /** @deprecated since Symfony 4.4, to be removed in Symfony 5.0. */
    public function eraseCredentials()
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        parent::eraseCredentials();
    }

    /** @deprecated since Symfony 4.4, to be removed in Symfony 5.0. */
    public function getAttributes(/* $hideDeprecation = false */)
    {
        if (0 === func_num_args() || false === func_get_arg(0)) {
            @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);
        }

        return parent::getAttributes();
    }

    /** @deprecated since Symfony 4.4, to be removed in Symfony 5.0. */
    public function setAttributes(array $attributes/*, $hideDeprecation = false */)
    {
        if (1 === func_num_args() || false === func_get_arg(1)) {
            @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);
        }

        parent::setAttributes($attributes);
    }

    /** @deprecated since Symfony 4.4, to be removed in Symfony 5.0. */
    public function hasAttribute($name)
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        return parent::hasAttribute($name);
    }

    /** @deprecated since Symfony 4.4, to be removed in Symfony 5.0. */
    public function getAttribute($name)
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        return parent::getAttribute($name);
    }

    /** @deprecated since Symfony 4.4, to be removed in Symfony 5.0. */
    public function setAttribute($name, $value)
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        parent::setAttribute($name, $value);
    }

    /** @deprecated since Symfony 4.4, to be removed in Symfony 5.0. */
    public function getCredentials()
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        return parent::getCredentials();
    }
}
