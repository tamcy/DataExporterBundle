<?php

namespace Sparkson\DataExporterBundle;

use Sparkson\DataExporterBundle\DependencyInjection\ColumnTypeCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SparksonDataExporterBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ColumnTypeCompilerPass());
    }
}
