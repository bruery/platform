<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\FormatterBundle\Controller;

use Sonata\FormatterBundle\Controller\CkeditorAdminController as BaseCkeditorAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CkeditorAdminController extends BaseCkeditorAdminController
{
    /**
     * Returns the response object associated with the browser action.
     *
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function browserAction(Request $request = null)
    {
        $this->checkIfMediaBundleIsLoaded();

        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $datagrid = $this->admin->getDatagrid();

        $filters = $this->getRequest()->get('filter');

        // set the default context
        if (!$filters) {
            $context = $this->admin->getPersistentParameter('context',  $this->get('sonata.media.pool')->getDefaultContext());
        } else {
            $context = $filters['context']['value'];
        }

        $datagrid->setValue('context', null, $context);

        if (!$filters || !array_key_exists('providerName', $filters)) {
            $provider = $this->getRequest()->get('provider')?: null;
        } else {
            $provider = $filters['providerName']['value'];
        }

        if ($provider) {
            $datagrid->setValue('providerName', null, $provider);
        } else {
            $datagrid->setValue('providerName', null, $this->admin->getPersistentParameter('provider'));
        }


        // retrieve the main category for the tree view
        $category = $this->container->get('sonata.classification.manager.category')->getRootCategory($context);

        if (!$filters) {
            $datagrid->setValue('category', null, $category->getId());
        }

        if ($request->get('category')) {
            $contextInCategory = $this->container->get('sonata.classification.manager.category')->findBy(array(
                'id'      => (int) $request->get('category'),
                'context' => $context,
            ));

            if (!empty($contextInCategory)) {
                $datagrid->setValue('category', null, $request->get('category'));
            } else {
                $datagrid->setValue('category', null, $category->getId());
            }
        }

        $formats = array();

        foreach ($datagrid->getResults() as $media) {
            $formats[$media->getId()] = $this->get('sonata.media.pool')->getFormatNamesByContext($media->getContext());
        }

        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render($this->getTemplate('browser'), array(
            'action'        => 'browser',
            'form'          => $formView,
            'datagrid'      => $datagrid,
            'root_category' => $category,
            'formats'       => $formats,
        ));
    }

    /**
     * Returns a template.
     *
     * @param string $name
     *
     * @return string
     */
    private function getTemplate($name)
    {
        $templates = $this->container->getParameter('sonata.formatter.ckeditor.configuration.templates');

        if (isset($templates[$name])) {
            return $templates[$name];
        }

        return;
    }

    /**
     * Checks if SonataMediaBundle is loaded otherwise throws an exception.
     *
     * @throws \RuntimeException
     */
    private function checkIfMediaBundleIsLoaded()
    {
        $bundles = $this->container->getParameter('kernel.bundles');

        if (!isset($bundles['SonataMediaBundle']) ||  !isset($bundles['BrueryMediaBundle'])) {
            throw new \RuntimeException('You cannot use this feature because you have to use SonataMediaBundle');
        }
    }
}
