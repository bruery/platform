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

use Bruery\MediaBundle\Provider\BaseProvider as Provider;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\MediaBundle\Model\GalleryInterface;

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
    public function prePersist(GalleryInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(GalleryInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(GalleryInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(GalleryInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, GalleryInterface $object)
    {
    }

    public function load(GalleryInterface $object)
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
