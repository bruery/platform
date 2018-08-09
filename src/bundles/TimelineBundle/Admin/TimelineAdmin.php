<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\TimelineBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class TimelineAdmin extends Admin
{
    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id', null, array('required' => false))
            ->add('type', null, array('required' => false))
        ;
    }

    /**
     * @param  \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $isCanonical = $this->getPersistentParameter('isCanonical');

        $listMapper
            ->addIdentifier('context')
            ->add('action.verb', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('type', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('id')
            ->add('type')
            ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('timeline', 'timeline');
    }
}
