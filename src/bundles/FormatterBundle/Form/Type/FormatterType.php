<?php

/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */
 */

namespace Bruery\FormatterBundle\Form\Type;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sonata\FormatterBundle\Formatter\Pool;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;

class FormatterType extends AbstractTypeExtension
{
    protected $pool;
    protected $ckeditorConfigManager;
    protected $translator;

    /**
     * @param \Sonata\FormatterBundle\Formatter\Pool             $pool
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(Pool $pool, TranslatorInterface $translator, $ckeditorConfigManager)
    {
        $this->pool = $pool;
        $this->translator = $translator;
        $this->ckeditorConfigManager = $ckeditorConfigManager;
    }

    /**
     * {@inheritdoc}
     *
     * @todo Remove it when bumping requirements to SF 2.7+
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $pool = $this->pool;
        $translator = $this->translator;

        $optionalOptions = array('selectpicker_enabled',
                'selectpicker_data_style',
                'selectpicker_title',
                'selectpicker_selected_text_format',
                'selectpicker_show_tick',
                'selectpicker_data_width',
                'selectpicker_data_size',
                'selectpicker_disabled',
                'selectpicker_dropup');

        if (method_exists($resolver, 'setDefined')) {
            $resolver->setDefined($optionalOptions);
        } else {
            // To keep Symfony <2.6 support
            $resolver->setOptional($optionalOptions);
        }

        $resolver->setDefaults(array(
            'inherit_data'      => true,
            'event_dispatcher'  => null,
            'format_field'      => null,
            'format_field_options' => array(
                'choices'           => $this->getChoices($pool, $translator)),
            'source_field' => null,
            'source_field_options'      => array(
                'attr' => array('class' => 'span10', 'rows' => 20)
            ),
            'target_field' => null,
            'listener'     => true,
            'ckeditor_toolbar_icons'    => array( array(
                'Bold', 'Italic', 'Underline',
                '-', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord',
                '-', 'Undo', 'Redo',
                '-', 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent',
                '-', 'Blockquote',
                '-', 'Image', 'Link', 'Unlink', 'Table'),
                array('Maximize', 'Source')
            ),
            'ckeditor_basepath'         => 'bundles/sonataformatter/vendor/ckeditor',
            'ckeditor_context'          => null,
            'selectpicker_enabled' => false,
        ));
    }

    protected function getChoices($pool, $translator)
    {
        $formatters = array();
        foreach ($pool->getFormatters() as $code => $instance) {
            $formatters[$code] = $translator->trans($code, array(), 'SonataFormatterBundle');
        }

        return $formatters;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'sonata_formatter_type';
    }
}
