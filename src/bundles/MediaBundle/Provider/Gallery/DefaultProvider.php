<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\MediaBundle\Provider\Gallery;

use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormBuilderInterface;

class DefaultProvider extends BaseProvider
{
    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, $object = null)
    {
        $this->buildCreateForm($formMapper, $object);
    }

    /**
     * {@inheritdoc}
     */
    public function buildCreateForm(FormMapper $formMapper, $object = null)
    {
        $formMapper
            ->tab('Details')
                ->with('bruery_gallery_settings')
                    ->add('settings', 'sonata_type_immutable_array', array('keys' => $this->getFormSettingsKeys($formMapper, $object), 'required'=>false, 'label'=>false, 'attr'=>array('class'=>'bruery-immutable-container')))
                ->end()
            ->end();
    }

    /**
     * @param FormMapper $formMapper
     * @param null $object
     * @return array
     */
    public function getFormSettingsKeys(FormMapper $formMapper, $object = null)
    {
        $settings = array(
            array('abstract', 'text', array('required' => false,)),
            array('content', 'sonata_formatter_type', function (FormBuilderInterface $formBuilder) {
                return array(
                    'event_dispatcher' => $formBuilder->getEventDispatcher(),
                    'format_field'     => array('format', '[format]'),
                    'source_field'     => array('rawContent', '[rawContent]'),
                    'target_field'     => '[content]',
                );
            }),
        );
        return $settings;
    }
}
