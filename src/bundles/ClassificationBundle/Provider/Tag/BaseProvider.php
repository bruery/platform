<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\ClassificationBundle\Provider\Tag;

use Bruery\ClassificationBundle\Provider\BaseProvider as Provider;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\CoreBundle\Validator\ErrorElement;

abstract class BaseProvider extends Provider
{
    protected $slugify;

    /**
     * @param string                                           $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }

    /**
     * @param mixed $rawSettings
     */
    public function setRawSettings($rawSettings)
    {
        parent::setRawSettings($rawSettings);
    }

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
            ->with('tab.group.bruery_classification_tag_settings')
                ->add('settings', 'sonata_type_immutable_array', array('keys' => $this->getFormSettingsKeys($formMapper, $object), 'required'=>false, 'label'=>false, 'attr'=>array('class'=>'bruery-immutable-container')))
            ->end();
    }


    /**
     * {@inheritdoc}
     */
    public function prePersist(TagInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(TagInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(TagInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(TagInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, TagInterface $object)
    {
    }

    public function load(TagInterface $object)
    {
    }

    /**
     * @return mixed
     */
    public function getSlugify()
    {
        return $this->slugify;
    }

    /**
     * @param mixed $slugify
     */
    public function setSlugify($slugify)
    {
        $this->slugify = $slugify;
    }
}
