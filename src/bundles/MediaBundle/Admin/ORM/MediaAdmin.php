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

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\MediaBundle\Admin\ORM\MediaAdmin as Admin;
use Sonata\MediaBundle\Provider\Pool;

class MediaAdmin extends Admin
{

    protected $datagridValues = array(
        '_page'       => 1,
        '_per_page'   => 8,
        '_sort_order' => 'DESC',
        '_sort_by'    => 'createdAt',
    );

    /**
     * @param string                   $code
     * @param string                   $class
     * @param string                   $baseControllerName
     * @param Pool                     $pool
     */
    public function __construct($code, $class, $baseControllerName, Pool $pool)
    {
        $this->maxPerPage = 8;
        $this->perPageOptions = array(8,12,16,32);
        parent::__construct($code, $class, $baseControllerName, $pool);
    }


    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('custom', 'string', array('template' => 'BrueryMediaBundle:MediaAdmin:list_media_custom.html.twig', 'label' => 'Media'))
            ->add('enabled', null, array('editable' => true, 'footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('createdAt', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
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
            ->add('name')
            ->add('providerReference')
            ->add('enabled')
            ->add('context', null, array(
                'show_filter' => $this->getPersistentParameter('hide_context') !== true,
            ), 'choice', $options)
            ->add('category', null, array(
                'show_filter' => false,
            ))
            ->add('width')
            ->add('height')
            ->add('contentType')
            ->add('createdAt', 'doctrine_orm_datetime_range', array('field_type' => 'sonata_type_datetime_range_picker'))
        ;

        $providers = array();

        $providerNames = (array) $this->pool->getProviderNamesByContext($this->getPersistentParameter('context', $this->pool->getDefaultContext()));
        foreach ($providerNames as $name) {
            $providers[$name] = $name;
        }

        $datagridMapper->add('providerName', 'doctrine_orm_choice', array(
            'field_options' => array(
                'choices'  => $providers,
                'required' => false,
                'multiple' => false,
                'expanded' => false,
            ),
            'field_type' => 'choice',
        ));
    }
}
