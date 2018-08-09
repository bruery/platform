<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\ClassificationBundle\Provider\Category;

use Bruery\ClassificationBundle\Provider\BaseProvider as Provider;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\ClassificationBundle\Model\CategoryInterface;
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
            ->tab('tab.bruery_classification_category_settings')
                ->with('tab.group.bruery_classification_category_settings', array('class' => 'col-md-8'))
                    ->add('settings', 'sonata_type_immutable_array', array('keys' => $this->getFormSettingsKeys($formMapper, $object), 'required'=>false, 'label'=>false, 'attr'=>array('class'=>'bruery-immutable-container')))
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(CategoryInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(CategoryInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(CategoryInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(CategoryInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, CategoryInterface $object)
    {
    }

    public function load(CategoryInterface $object)
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
