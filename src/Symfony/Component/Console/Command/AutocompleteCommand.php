<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Command;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class AutocompleteCommand extends Command
{
    public function __construct()
    {
        parent::__construct('_autocomplete');
    }

    protected function configure()
    {
        $this
            ->addOption('global-args', '', InputOption::VALUE_NONE)
            ->addOption('command', '', InputOption::VALUE_OPTIONAL, '', false)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $p = Process::fromShellCommandline('echo $SHELL');
            $shell = basename(rtrim($p->mustRun()->getOutput()));
            if ('zsh' !== $shell) {
                throw new \RuntimeException(sprintf('Unsupported shell: "%s"', $shell));
            }

            $output->writeln($this->autocompleteZsh($input));
        } catch (\Throwable $e) {
            $output->writeln($e->getMessage());
        }

        return 0;
    }

    private function autocompleteZsh(InputInterface $input): array
    {
        if ($input->getOption('global-args')) {
            return $this->autocompleteZshInputDefinition($this->getApplication()->getDefinition());
        }

        if (false !== ($cmd = $input->getOption('command'))) {
            if (!$cmd) {
                $zshDescribe = [];
                foreach ($this->getApplication()->all() as $command) {
                    $zshDescribe[] = sprintf('%s:%s', str_replace(':', '\:', $command->getName()), $command->getDescription());
                }

                return $zshDescribe;
            }

            return $this->autocompleteZshInputDefinition($this->getApplication()->find($cmd)->getDefinition());
        }

        return [];
    }

    private function autocompleteZshInputDefinition(InputDefinition $inputDefinition): array
    {
        $zshArguments = [];
        foreach ($inputDefinition->getOptions() as $inputOption) {
            $name = $inputOption->getName();
            if ('verbose' === $name) {
                $zshArguments[] = '(-q --quiet --verbose)*-v[increase output verbosity]';
                $zshArguments[] = '(-q --quiet --verbose)--verbose[increase output verbosity]';
                continue;
            } elseif ('quiet' === $name) {
                $zshArguments[] = '(-q -v --quiet --verbose)-q[reduce output verbosity]';
                $zshArguments[] = '(-q -v --quiet --verbose)--quiet[reduce output verbosity]';
                continue;
            }

            $alternative = $inputOption->isNegatable() ? '-no-'.$name : $inputOption->getShortcut();
            $description = $inputOption->getDescription();
            if ($alternative) {
                $zshArguments[] = sprintf('(--%s -%s)--%1$s[%s]', $name, $alternative, $description);
                $zshArguments[] = sprintf('(--%s -%s)-%2$s[%s]', $name, $alternative, $description);
            } else {
                $zshArguments[] = sprintf('--%1s[%s]', $name, $description);
            }
        }

        return $zshArguments;
    }
}

/*
[
                '(-h --help)-h[display help information]',
                '(-h --help)--help[display help information]',

                '(: * -)-V[display version information]',
                '(: * -)--version[display version information]',

                '(-q -v --quiet --verbose)-q[reduce output verbosity]',
                '(-q -v --quiet --verbose)--quiet}[reduce output verbosity]',
                '(-q --quiet --verbose)*-v[increase output verbosity]',
                '(-q --quiet --verbose)--verbose[increase output verbosity]',

                '(--no-ansi)--ansi[force ANSI (color) output]',
                '(--ansi)--no-ansi[disable ANSI (color) output]',

                '(-n --no-interaction)-n[run non-interactively]',
                '(-n --no-interaction)--no-interaction[run non-interactively]',
            ]
 */
