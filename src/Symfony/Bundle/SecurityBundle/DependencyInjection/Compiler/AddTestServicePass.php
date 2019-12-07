<?php

namespace Symfony\Bundle\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Bundle\SecurityBundle\Security\TestTokenStorage;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddTestServicePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (
            !$container->hasDefinition('test.service_container')
            || (
                !($hasUntracked = $container->hasDefinition('security.untracked_token_storage'))
                && !$container->hasDefinition('security.token_storage')
            )
        ) {
            $container->removeDefinition('security.test.user_provider_map');

            return;
        }

        $container->getDefinition('security.test.user_provider_map')->setPublic(true);

        $container->register(TestTokenStorage::class)
            ->setClass(TestTokenStorage::class)
            ->setDecoratedService($hasUntracked ? 'security.untracked_token_storage' : 'security.token_storage')
            ->setArguments([new Reference(TestTokenStorage::class.'.inner')]);
    }
}
