<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\WebProfilerBundle\Twig;

use Symfony\Component\HttpKernel\DataCollector\Util\ValueExporter;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * Twig extension for the profiler.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class WebProfilerExtension extends \Twig_Extension
{
    /**
     * @var ValueExporter
     */
    private $valueExporter;
    /**
     * @var HtmlDumper
     */
    private $dumper;

    public function __construct(HtmlDumper $dumper = null)
    {
        $this->dumper = $dumper ?: new HtmlDumper();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('profiler_dump', array($this, 'dumpValue')),
        );
    }

    public function dumpValue($value)
    {
        if ($value instanceof Data) {
            $dump = fopen('php://memory', 'r+b');
            $prevOutput = $this->dumper->setOutput($dump);

            $this->dumper->setOutput($prevOutput);
            rewind($dump);

            return stream_get_contents($dump);
        }

        @trigger_error('Dumping non-cloned data is deprecated since version 3.2 and will be removed in 4.0. Use DataCollector::cloneVar().', E_USER_DEPRECATED);

        if (null === $this->valueExporter) {
            $this->valueExporter = new ValueExporter();
        }

        return $this->valueExporter->exportValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'profiler';
    }
}
