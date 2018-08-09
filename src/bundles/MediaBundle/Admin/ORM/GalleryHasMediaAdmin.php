<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\MediaBundle\Admin\ORM;

use Bruery\CoreBundle\Admin\AdminProviderInterface;
use Bruery\CoreBundle\Provider\PoolInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;

class GalleryHasMediaAdmin extends AbstractGalleryHasMediaAdmin implements AdminProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function setSubject($subject)
    {
        parent::setSubject($subject);
        $this->provider = $this->getPoolProvider($this->getPool());
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->hasProvider()) {
            // define group zoning
            $formMapper
                ->tab('Media')
                    ->with('bruery_gallery_has_media_media',  array('class' => 'col-md-12'))->end()
                ->end()
                ->tab('Settings')
                    ->with('bruery_gallery_has_media_settings',  array('class' => 'col-md-6'))->end()
                ->end();
        } else {
            // define group zoning
            $formMapper
                ->tab('Media')
                    ->with('bruery_gallery_has_media_media',  array('class' => 'col-md-12'))->end()
                ->end();
        }


        $formMapper
            ->tab('Media')
                ->with('bruery_gallery_has_media_media',  array('class' => 'col-md-12'))
                    ->add('media', 'sonata_type_model_list', array('required' => false), array(
                        'link_parameters' => $this->getMediaSettings(),
                    ))
                    ->add('enabled', null, array('required' => false))
                    ->add('position', 'hidden')
                ->end()
            ->end();

        if ($this->hasProvider()) {
            $instance = $this->getSubject();
            if ($instance && $instance->getId()) {
                $this->provider->load($instance);
                $this->provider->buildEditForm($formMapper);
            } else {
                $this->provider->buildCreateForm($formMapper);
            }
        }
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('media')
            ->add('gallery')
            ->add('position')
            ->add('enabled');
    }


    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters()
    {
        $parameters = parent::getPersistentParameters();

        if ($this->hasRequest() && $this->getRequest()->get('collection')) {
            $parameters['collection'] = $this->getRequest()->get('collection');
        }

        if (is_array($parameters) && isset($parameters['collection'])) {
            $parameters = array_merge($parameters, array('hide_collection' => $this->hasRequest() ? (int) $this->getRequest()->get('hide_collection', 0) : 0));
        } else {
            $collectionSlug = $this->getSlugify()->slugify($this->getDefaultCollection());
            $parameters = array(
                'collection'      => $collectionSlug,
                'hide_collection' => $this->hasRequest() ? (int) $this->getRequest()->get('hide_collection', 0) : 0);
        }

        if ($this->getSubject() && $this->getSubject()->getGallery()) {
            $parameters['collection'] = $this->getSubject()->getGallery()->getCollection() ? $this->getSubject()->getGallery()->getCollection()->getSlug() : $collectionSlug;
            return $parameters;
        }

        return $parameters;
    }

    public function fetchProviderKey()
    {
        $collectionSlug = $this->getPersistentParameter('collection');

        $collection = null;
        if ($collectionSlug) {
            $collection = $this->collectionManager->findOneBy(array('slug'=>$collectionSlug));
        }

        if ($collection) {
            return $collection;
        } else {
            return;
        }
    }

    public function getPoolProvider(PoolInterface $pool)
    {
        $currentCollection = $this->fetchProviderKey();

        if ($pool->hasCollection($currentCollection->getSlug())) {
            $providerName = $pool->getProviderNameByCollection($currentCollection->getSlug());

            if (!$providerName) {
                return null;
            }
            $provider = $pool->getProvider($providerName);
            $params = $pool->getSettingsByCollection($currentCollection->getSlug());
            $provider = $pool->getProvider($providerName);
            ###############################
            # Load provoder levelsettings
            ###############################
            $provider->setRawSettings($params);
            return $provider;
        }
        return null;
    }

    public function getProviderName(PoolInterface $pool, $providerKey = null)
    {
        if (!$providerKey) {
            $providerKey = $this->fetchProviderKey();
        }

        if ($providerKey && $pool->hasCollection($providerKey->getSlug())) {
            return $pool->getProviderNameByCollection($providerKey->getSlug());
        }

        return null;
    }

    public function getMediaSettings()
    {
        $settings = parent::getMediaSettings();

        if (!$this->hasProvider()) {
            return $settings;
        }

        $providerSettings = [];
        $providerSettings = $this->getProvider()->getMediaSettings();

        if ($providerSettings) {
            $settings = array_merge($settings, $providerSettings);
        }

        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        parent::prePersist($object);
        if ($this->hasProvider()) {
            $this->getProvider()->prePersist($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        parent::preUpdate($object);

        if ($this->hasProvider()) {
            $this->getProvider()->preUpdate($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate($object)
    {
        parent::postUpdate($object);
        if ($this->hasProvider()) {
            $this->getProvider()->postUpdate($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist($object)
    {
        parent::postPersist($object);
        if ($this->hasProvider()) {
            $this->getProvider()->postPersist($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        parent::validate($errorElement, $object);
        if ($this->hasProvider()) {
            $this->getProvider()->validate($errorElement, $object);
        }
    }
}
