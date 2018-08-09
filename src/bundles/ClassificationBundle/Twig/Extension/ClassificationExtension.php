<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\ClassificationBundle\Twig\Extension;

use Sonata\CoreBundle\Model\ManagerInterface;

class ClassificationExtension extends \Twig_Extension  implements \Twig_Extension_InitRuntimeInterface
{
    /**
     * @var CmsManagerSelectorInterface
     */
    private $contextManager;
    private $categoryManager;

    /**
     * @param ManagerInterface $contextManager
     * @param ManagerInterface $categoryManager
     *
     */
    public function __construct(ManagerInterface $contextManager, ManagerInterface $categoryManager)
    {
        $this->contextManager     = $contextManager;
        $this->categoryManager    = $categoryManager;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('bruery_classification_allow_create_base_category', array($this, 'allowCreateBaseCategory')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'bruery_classification';
    }

    /**
     * @return string
     */
    public function allowCreateBaseCategory()
    {
        $categoryContext = $this->categoryManager->getCategoryContexts();
        try {
            $defunctContexts = $this->contextManager->getDefunctContext($categoryContext);
            if ($defunctContexts && is_array($defunctContexts)) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
