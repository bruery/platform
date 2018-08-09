<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\ClassificationBundle\Provider\Collection;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\FormBuilderInterface;

class DefaultProvider extends BaseProvider
{
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

    public function load(CollectionInterface $object)
    {
    }

    public function validate(ErrorElement $errorElement, CollectionInterface $object)
    {
    }
}
