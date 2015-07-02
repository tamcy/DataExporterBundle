<?php

namespace Sparkson\DataExporterBundle\Exporter\Core\Type;

use Sparkson\DataExporterBundle\Exporter\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A raw type.
 *
 * This type returns the field value as-is.
 * Can be useful when building nested columns.
 *
 * @author Tamcy <tamcyhk@outlook.com>
 */
class RawType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'compound' => false,
        ));
    }

    public function getName()
    {
        return 'raw';
    }

}