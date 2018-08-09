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

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;

class TagAdminController extends Controller
{

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request = null)
    {
        $contextManager = $this->get('sonata.classification.manager.context');
        $defaultContext  = $this->container->getParameter('bruery.classification.tag.default_context');

        $currentContext = null;

        if ($context = $request->get('context')) {
            $currentContext = $contextManager->find($context);
        } else {
            $currentContext = $contextManager->find($defaultContext);
        }

        $contexts = $contextManager->findAll();

        if (!$currentContext) {
            $currentContext = array_shift($contexts);
        }

        $this->admin->checkAccess('list');

        $preResponse = $this->preList($request);
        if ($preResponse !== null) {
            return $preResponse;
        }

        if ($listMode = $request->get('_list_mode')) {
            $this->admin->setListMode($listMode);
        }

        $datagrid = $this->admin->getDatagrid();

        if ($this->admin->getPersistentParameter('context')) {
            $datagrid->setValue('context', null, $this->admin->getPersistentParameter('context'));
        } else {
            $datagrid->setValue('context', null, $currentContext->getId());
        }

        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render($this->admin->getTemplate('list'), array(
            'action'           => 'list',
            'current_context'  => $currentContext,
            'contexts'         => $contexts,
            'form'             => $formView,
            'datagrid'         => $datagrid,
            'csrf_token'       => $this->getCsrfToken('sonata.batch'),
        ), null, $request);
    }
}
