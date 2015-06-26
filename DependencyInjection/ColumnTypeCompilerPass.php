<?php

namespace Sparkson\DataExporterBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ColumnTypeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sparkson.data_exporter.type_registry')) {
            return;
        }

        $definition = $container->findDefinition(
            'sparkson.data_exporter.type_registry'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'sparkson.data_exporter.type'
        );

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addType',
                array(new Reference($id))
            );
        }
    }

}