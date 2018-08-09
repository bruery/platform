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

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\ClassificationBundle\Admin\ContextAdmin as Admin;

class ContextAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->ifTrue(!($this->hasSubject() && $this->getSubject()->getId() !== null))
                ->add('id')
            ->ifEnd()
            ->add('name')
            ->add('enabled', null, array(
                'required' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('enabled')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('name', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('enabled', null, array('editable' => true, 'footable'=>array('attr'=>array('data-breakpoints'=>array('xs')))))
            ->add('createdAt', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('updatedAt', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
        ;
    }
}
