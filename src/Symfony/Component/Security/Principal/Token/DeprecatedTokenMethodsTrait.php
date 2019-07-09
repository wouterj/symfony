<?php

namespace Symfony\Component\Security\Principal\Token;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 * @internal
 * @deprecated since Symfony 4.4, to be removed in Symfony 5.0.
 */
trait DeprecatedTokenMethodsTrait
{
    public function getUsername()
    {
        @trigger_error('Method '.__METHOD__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        return parent::getUsername();
    }

    public function getUser()
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        return parent::getUser();
    }

    public function setUser($user)
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        parent::setUser($user);
    }

    public function isAuthenticated()
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0. Check instance of '.__NAMESPACE__.'\\AuthenticatedTokenInterface instead.', E_USER_DEPRECATED);

        return parent::isAuthenticated();
    }

    public function setAuthenticated($authenticated)
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        parent::setAuthenticated($authenticated);
    }

    public function eraseCredentials()
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        parent::eraseCredentials();
    }

    public function getAttributes()
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        return parent::getAttributes();
    }

    public function setAttributes(array $attributes)
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        parent::setAttributes($attributes);
    }

    public function hasAttribute($name)
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        return parent::hasAttribute($name);
    }

    public function getAttribute($name)
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        return parent::getAttribute($name);
    }

    public function setAttribute($name, $value)
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        parent::setAttribute($name, $value);
    }

    public function getCredentials()
    {
        @trigger_error('Method '.get_class($this).'::'.__FUNCTION__.' is deprecated since Symfony 4.4 and will be removed in 5.0.', E_USER_DEPRECATED);

        return parent::getCredentials();
    }
}
