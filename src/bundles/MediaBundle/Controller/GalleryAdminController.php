<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\MediaBundle\Controller;

use Sonata\MediaBundle\Controller\GalleryAdminController as Controller;
use Symfony\Component\HttpFoundation\Request;

class GalleryAdminController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request = null)
    {
        $this->admin->checkAccess('list');

        $preResponse = $this->preList($request);
        if ($preResponse !== null) {
            return $preResponse;
        }

        if ($listMode = $request->get('_list_mode', 'mosaic')) {
            $this->admin->setListMode($listMode);
        }

        $datagrid = $this->admin->getDatagrid();

        $filters = $request->get('filter');

        // set the default context
        if (!$filters || !array_key_exists('context', $filters) || !$filters['context']['value']) {
            $contextSlug = $this->admin->getPersistentParameter('context',  $this->get('sonata.media.pool')->getDefaultContext());
        } else {
            $contextSlug = $filters['context']['value'];
        }

        $contextManager = $this->get('sonata.classification.manager.context');
        $context = $contextManager->findOneBy(array('id'=>$contextSlug));

        if (!$context) {
            throw $this->createNotFoundException($this->get('translator')->trans('media_context_not_found', array('%context_slug%'=>$contextSlug), 'SonataMediaBundle'));
        }

        $datagrid->setValue('context', null, $context->getId());

        $collectiontManager = $this->get('sonata.classification.manager.collection');
        $currentCollection = null;

        //Gallery Collection Context
        $slugify = $this->get($this->container->getParameter('bruery.media.slugify_service'));
        $defaultGalleryContext = $this->container->getParameter('bruery.media.gallery.default_context');
        $galleryContext = $contextManager->findOneBy(array('id'=>$slugify->slugify($defaultGalleryContext)));

        if (!$galleryContext && !$galleryContext instanceof \Sonata\ClassificationBundle\Model\ContextInterface) {
            $galleryContext = $contextManager->generateDefaultContext($defaultGalleryContext);
        }

        $defaultGalleryCollection = $this->container->getParameter('bruery.media.gallery.default_collection');

        if ($collection = $request->get('collection')) {
            $currentCollection = $collectiontManager->findOneBy(array('slug'=>$slugify->slugify($collection), 'context'=>$galleryContext));
        } else {
            $currentCollection = $collectiontManager->findOneBy(array('slug'=>$slugify->slugify($defaultGalleryCollection), 'context'=>$galleryContext));
        }

        $collections = $collectiontManager->findBy(array('context'=>$galleryContext));

        if (!$currentCollection &&
           !$currentCollection instanceof \Sonata\ClassificationBundle\Model\CollectionInterface &&
            count($collections) === 0) {
            $currentCollection = $collectiontManager->generateDefaultCollection($galleryContext, $defaultGalleryCollection);
            $collections = $collectiontManager->findBy(array('context'=>$galleryContext));
        }

        if (count($collections)>0) {
            if (!$currentCollection) {
                $currentCollection = current(array_shift($collections));
            }

            if ($this->admin->getPersistentParameter('collection')) {
                $collection = $collectiontManager->findOneBy(array('context'=>$galleryContext, 'slug'=>$this->admin->getPersistentParameter('collection')));
                if ($collection && $collection instanceof \Sonata\ClassificationBundle\Model\CollectionInterface) {
                    $datagrid->setValue('collection', null, $collection->getId());
                } else {
                    throw $this->createNotFoundException($this->get('translator')->trans('page_not_found', array(), 'SonataAdminBundle'));
                }
            } else {
                $datagrid->setValue('collection', null, $currentCollection->getId());
            }
        }

        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render($this->admin->getTemplate('list'), array(
            'action'                => 'list',
            'current_collection'    => $currentCollection,
            'collections'           => $collections,
            'context'               => $context,
            'form'                  => $formView,
            'datagrid'              => $datagrid,
            'csrf_token'            => $this->getCsrfToken('sonata.batch'),
        ), null, $request);
    }
}
