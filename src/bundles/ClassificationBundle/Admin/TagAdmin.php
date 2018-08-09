<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\ClassificationBundle\Admin;

use Bruery\ClassificationBundle\Admin\AbstractTagAdmin as Admin;
use Bruery\CoreBundle\Admin\AdminProviderInterface;
use Bruery\CoreBundle\Provider\PoolInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;

class TagAdmin extends Admin
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
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->hasProvider()) {
            $tabSettings = array('class' => 'col-md-4');
        } else {
            $tabSettings = array('class' => 'col-md-8');
        }

        $formMapper->with('tab.group.bruery_classification_tag_general', $tabSettings)->end();

        if ($this->hasProvider()) {
            $formMapper->with('tab.group.bruery_classification_tag_settings', array('class' => 'col-md-8'))->end();
        }


        $formMapper
            ->with('tab.group.bruery_classification_tag_general')
                ->add('name')
            ->end()
        ;

        if ($this->hasSubject() && $this->getSubject()->getId()) {
            $formMapper
                ->with('tab.group.bruery_classification_tag_general')
                    ->add('slug')
                ->end();
        }

        $formMapper
            ->with('tab.group.bruery_classification_tag_general')
                ->add('enabled', null, array('required' => false))
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
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('enabled')
            ->add('context', null, array('show_filter' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('slug', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm', 'md')))))
            ->add('enabled', null, array('editable' => true, 'footable'=>array('attr'=>array('data-breakpoints'=>array('xs')))))
            ->add('createdAt', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('updatedAt', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
        ;
    }
    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters()
    {
        $parameters = array(
            'context'      => $this->getDefaultContext(),
            'hide_context' => $this->hasRequest() ? (int) $this->getRequest()->get('hide_context', 0) : 0,
        );

        if ($this->getSubject()) {
            $parameters['context'] = $this->getSubject()->getContext() ? $this->getSubject()->getContext()->getId() : $this->getDefaultContext();

            return $parameters;
        }

        if ($this->hasRequest()) {
            $parameters['context'] = $this->getRequest()->get('context');

            return $parameters;
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();

        if ($contextId = $this->getPersistentParameter('context')) {
            $context = $this->contextManager->find($contextId);
            if (!$context) {
                $context = $this->contextManager->create();
                $context->setEnabled(true);
                $context->setId($context);
                $context->setName($context);

                $this->contextManager->save($context);
            }

            $instance->setContext($context);
        }

        return $instance;
    }

    public function fetchProviderKey()
    {
        $contextCode = $this->getPersistentParameter('context');

        $context = null;
        if ($contextCode) {
            $context = $this->contextManager->find($contextCode);
        } else {
            $context = $this->contextManager->find($this->getDefaultContext());
        }

        if ($context) {
            return $context;
        } else {
            return;
        }
    }

    public function getPoolProvider(PoolInterface $pool)
    {
        $currentContext = $this->fetchProviderKey();

        if ($this->pool->hasContext($currentContext->getId())) {
            $providerName = $this->pool->getProviderNameByContext($currentContext->getId());
            return $this->pool->getProvider($providerName);
        }

        return;
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
