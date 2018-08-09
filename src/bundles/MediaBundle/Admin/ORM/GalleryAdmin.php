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
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;

class GalleryAdmin extends AbstractGalleryAdmin implements AdminProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function setSubject($subject)
    {
        parent::setSubject($subject);
        $this->galleryProvider = $this->getPoolProvider($this->getGalleryPool());
        $this->childGalleryProvider = $this->getPoolProvider($this->getChildGalleryPool());
    }


    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $context = $this->getPersistentParameter('context');

        if (!$context) {
            $context = $this->pool->getDefaultContext();
        }

        $formats = array();
        foreach ((array) $this->pool->getFormatNamesByContext($context) as $name => $options) {
            $formats[$name] = $name;
        }

        $contexts = array();
        foreach ((array) $this->pool->getContexts() as $contextItem => $format) {
            $contexts[$contextItem] = $contextItem;
        }

        $mediaFieldOptions = array(
            'edit'              => 'inline',
            'sortable'          => 'position',
            'link_parameters'   => $this->getPersistentParameters(),
            'admin_code'        => 'sonata.media.admin.gallery_has_media',
        );

        if ($this->hasChildGalleryProvider()) {
            $mediaFieldOptions['inline'] = 'standard';
            $mediaTabSettings = array('class' => 'col-md-8');
        } else {
            $mediaFieldOptions['inline'] = 'table';
            $mediaTabSettings = array('class' => 'col-md-12');
        }

        if ($this->hasGalleryProvider()) {
            $formMapper
                ->tab('Details')
                    ->with('bruery_gallery_settings',  array('class' => 'col-md-8'))->end()
                    ->with('bruery_gallery_options',  array('class' => 'col-md-4'))->end()
                ->end()
                ->tab('Media')
                    ->with('bruery_gallery_gallery',  $mediaTabSettings)->end()
                ->end();
        } else {
            $formMapper
                ->tab('Details')
                    ->with('bruery_gallery_options',  array('class' => 'col-md-8'))->end()
                ->end()
                ->tab('Media')
                    ->with('bruery_gallery_gallery',  $mediaTabSettings)->end()
                ->end()
            ;
        }

        $formMapper
            ->tab('Details')
                ->with('bruery_gallery_options')
                    ->add('enabled', null, array('required' => false))
                    ->add('name')
                    ->add('defaultFormat', 'choice', array('choices' => $formats))
                ->end()
            ->end()
        ;


        $formMapper
            ->tab('Media')
                ->with('bruery_gallery_gallery')
                    ->add('galleryHasMedias', 'sonata_type_collection', array('cascade_validation' => true), $mediaFieldOptions)
                ->end()
            ->end()
        ;

        if ($this->hasGalleryProvider()) {
            $instance = $this->getSubject();

            if ($instance && $instance->getId()) {
                $this->galleryProvider->load($instance);
                $this->galleryProvider->buildEditForm($formMapper);
            } else {
                $this->galleryProvider->buildCreateForm($formMapper);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('enabled', 'boolean', array('editable' => true, 'footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('context', 'trans', array('catalogue' => 'SonataMediaBundle', 'footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('defaultFormat', 'trans', array('catalogue' => 'SonataMediaBundle', 'footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $options = array(
            'choices' => array(),
        );

        foreach ($this->pool->getContexts() as $name => $context) {
            $options['choices'][$name] = $name;
        }

        $datagridMapper
            ->add('collection', 'doctrine_orm_model_autocomplete', array('show_filter' => false), null, array(
                'property' => 'name',
                'callback' => function ($admin, $property, $value) {
                    $datagrid = $admin->getDatagrid();
                    $queryBuilder = $datagrid->getQuery();
                    $queryBuilder->andWhere(sprintf('%s.context = :context', $queryBuilder->getRootAlias()));
                    $queryBuilder->setParameter('context', $this->getDefaultContext());
                }

            ))
            ->add('context', null, array(
                'show_filter' => $this->getPersistentParameter('hide_context') !== true,
                #'show_filter' => false,
            ), 'choice', $options)
            ->add('name')
            ->add('enabled')
        ;
    }

    public function fetchProviderKey()
    {
        $collectionSlug = $this->getPersistentParameter('collection');
        $collection = null;
        if ($collectionSlug) {
            $collection = $this->collectionManager->findOneBy(array('slug'=>$collectionSlug));
        } else {
            $collection = $this->collectionManager->findOneBy(array('slug'=>$this->getDefaultCollection()));
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

        if ($this->getSubject()) {
            $parameters['collection'] = $this->getSubject()->getCollection() ? $this->getSubject()->getCollection()->getSlug() : $collectionSlug;
            return $parameters;
        }

        return array_merge($parameters, array(
            'context'  => $this->getRequest()->get('context', $this->pool->getDefaultContext()),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();

        if ($this->hasRequest()) {
            $contextSlug = $this->getSlugify()->slugify($this->getRequest()->get('context'));
            $instance->setContext($contextSlug);
        }

        $galleryContext = $this->contextManager->findOneBy(array('id'=>$this->getSlugify()->slugify($this->getDefaultContext())));

        if (!$galleryContext && !$galleryContext instanceof \Sonata\ClassificationBundle\Model\ContextInterface) {
            $galleryContext = $this->getContextManager->generateDefaultContext($this->getDefaultContext());
        }

        $collectionSlug = $this->getPersistentParameter('collection') ?: $this->getSlugify()->slugify($this->getDefaultCollection());
        $collections = $this->collectionManager->findBy(array('context'=>$galleryContext));
        $collection = $this->collectionManager->findOneBy(array('slug'=>$collectionSlug, 'context'=>$galleryContext));


        if (!$collections && !$collection && !$collection instanceof \Sonata\ClassificationBundle\Model\CollectionInterface) {
            $collection = $this->collectionManager->generateDefaultCollection($galleryContext, $this->getDefaultCollection());
        }

        $instance->setCollection($collection);

        return $instance;
    }

    public function getGalleryHasMediaSettings()
    {
        $settings = [];
        $settings['collection'] = $this->getPersistentParameter('collection');
        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        parent::prePersist($object);
        if ($this->hasGalleryProvider()) {
            $this->getGalleryProvider()->prePersist($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        parent::preUpdate($object);

        if ($this->hasGalleryProvider()) {
            $this->getGalleryProvider()->preUpdate($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate($object)
    {
        parent::postUpdate($object);
        if ($this->hasGalleryProvider()) {
            $this->getGalleryProvider()->postUpdate($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist($object)
    {
        parent::postPersist($object);
        if ($this->hasGalleryProvider()) {
            $this->getGalleryProvider()->postPersist($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        parent::validate($errorElement, $object);
        if ($this->hasGalleryProvider()) {
            $this->getGalleryProvider()->validate($errorElement, $object);
        }
    }
}
