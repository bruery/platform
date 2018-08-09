<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\MediaBundle\Provider\GalleryHasMedia;

use Bruery\MediaBundle\Provider\BaseProvider as Provider;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\MediaBundle\Model\GalleryHasMediaInterface;

abstract class BaseProvider extends Provider
{
    protected $slugify;
    protected $categoryManager;

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
    public function prePersist(GalleryHasMediaInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(GalleryHasMediaInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(GalleryHasMediaInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(GalleryHasMediaInterface $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, GalleryHasMediaInterface $object)
    {
    }

    public function load(GalleryHasMediaInterface $object)
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

    /**
     * @return mixed
     */
    public function getCategoryManager()
    {
        return $this->categoryManager;
    }

    /**
     * @param mixed $categoryManager
     */
    public function setCategoryManager($categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    public function getMediaSettings()
    {
        $params = $this->getSetting('media');
        $settings = [];
        if ($params) {
            $default = isset($this->defaultSettings['post_has_media']) && isset($this->defaultSettings['post_has_media']['default_context']) ? $this->defaultSettings['post_has_media']['default_context'] : null;
            $settings['context'] = isset($params['context']) && $params['context'] !== null ? $params['context'] : $default;

            $default = isset($this->defaultSettings['post_has_media']) && isset($this->defaultSettings['post_has_media']['hide_context']) ? $this->defaultSettings['post_has_media']['hide_context'] : false;
            $settings['hide_context'] = isset($params['hide_context']) && $params['hide_context'] !== null ? $params['hide_context'] : $default;


            $default = isset($this->defaultSettings['post_has_media']) && isset($this->defaultSettings['post_has_media']['default_category']) ? $this->defaultSettings['post_has_media']['default_category'] : null;
            $category = isset($params['category']) && $params['category'] !== null ? $params['category'] : $default;

            if ($category !== null) {
                $category = $this->categoryManager->findOneBy(array('slug'=>$this->getSlugify()->slugify($params['category']), 'context'=>$settings['context']));
                if ($category) {
                    $settings['category'] = $category->getId();
                }
            }
        }
        return $settings;
    }
}
