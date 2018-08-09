<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\ClassificationBundle\Controller;

use Sonata\ClassificationBundle\Controller\CategoryAdminController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CategoryAdminController extends Controller
{
    /**
     * Create action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws AccessDeniedException If access is not granted
     */
    public function createBaseCategoryAction(Request $request = null)
    {
        $categoryContext = $this->get('sonata.classification.manager.category')->getCategoryContexts();
        $defunctContexts = $this->get('sonata.classification.manager.context')->getDefunctContext($categoryContext);

        if (!$defunctContexts || !is_array($defunctContexts)) {
            $this->addFlash('sonata_flash_error', $this->admin->trans('flash_category_context_not_available', array(), 'SonataClassificationBundle'));
            return new RedirectResponse($this->admin->generateUrl('tree'));
        }

        if (!$request->get('context') && $request->isMethod('get')) {
            return $this->render('BrueryClassificationBundle:CategoryAdmin:select_context.html.twig', array(
                'defunct_contexts'  => $defunctContexts,
                'base_template'     => $this->getBaseTemplate(),
                'admin'             => $this->admin,
                'action'            => 'create',
            ));
        }

        $this->addFlash('sonata_flash_success', $this->admin->trans('flash_category_context_created', array('%context%'=>$request->get('context')), 'SonataClassificationBundle'));
        return new RedirectResponse($this->admin->generateUrl('create', array('context'=>$request->get('context'))));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request = null)
    {
        if (!$request->get('filter') && !$request->get('filters')) {
            return new RedirectResponse($this->admin->generateUrl('tree'));
        }

        if ($listMode = $request->get('_list_mode')) {
            $this->admin->setListMode($listMode);
        }

        $categoryManager = $this->get('sonata.classification.manager.category');
        $contextManager = $this->get('sonata.classification.manager.context');

        $currentContext = null;
        $filters = $request->get('filter');

        if ($filters && array_key_exists('context', $filters) && $filters['context']['value']) {
            $currentContext = $contextManager->find($filters['context']['value']);
        } elseif ($context = $request->get('context')) {
            $currentContext = $contextManager->find($context);
        } else {
            $currentContext = $contextManager->find($this->container->getParameter('bruery.classification.category.default_context'));
        }

        $rootCategories = $categoryManager->getRootCategories(false);

        if (!$currentContext) {
            $mainCategory   = current($rootCategories);
            $currentContext = $mainCategory->getContext();
        } else {
            foreach ($rootCategories as $category) {
                if ($currentContext->getId() != $category->getContext()->getId()) {
                    continue;
                }

                $mainCategory = $category;

                break;
            }
        }

        $datagrid = $this->admin->getDatagrid();

        if ($currentContext) {
            $datagrid->setValue('context', null, $currentContext->getId());
        }

        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render($this->admin->getTemplate('list'), array(
            'action'     => 'list',
            'main_category'    => $mainCategory,
            'root_categories'  => $rootCategories,
            'current_context'  => $currentContext,
            'form'       => $formView,
            'datagrid'   => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function treeAction(Request $request)
    {
        $categoryManager = $this->get('sonata.classification.manager.category');
        $contextManager  = $this->get('sonata.classification.manager.context');
        $defaultContext  = $this->container->getParameter('bruery.classification.category.default_context');

        $currentContext = false;
        if ($context = $request->get('context')) {
            $currentContext = $contextManager->find($context);
        } else {
            $currentContext = $contextManager->find($defaultContext);
        }

        $rootCategories = $categoryManager->getRootCategories(false);

        $mainCategory = null;

        if (!$currentContext) {
            $mainCategory   = current($rootCategories);
            $currentContext = $mainCategory->getContext();
        } else {
            foreach ($rootCategories as $category) {
                if ($currentContext->getId() != $category->getContext()->getId()) {
                    continue;
                }

                $mainCategory = $category;

                break;
            }
        }

        if (!$mainCategory) {
            $mainCategory = $categoryManager->generateParentCategory($currentContext, $defaultContext);
        }

        $datagrid = $this->admin->getDatagrid();

        if ($currentContext) {
            $datagrid->setValue('context', null, $currentContext->getId());
        }

        $formView = $datagrid->getForm()->createView();

        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render('BrueryClassificationBundle:CategoryAdmin:tree.html.twig', array(
            'action'           => 'tree',
            'main_category'    => $mainCategory,
            'root_categories'  => $rootCategories,
            'current_context'  => $currentContext,
            'form'             => $formView,
            'csrf_token'       => $this->getCsrfToken('sonata.batch'),
        ));
    }
}
