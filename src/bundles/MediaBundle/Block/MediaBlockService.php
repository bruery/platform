<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\MediaBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\MediaBundle\Block\MediaBlockService as BaseMediaBlockService;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MediaBlockService extends BaseMediaBlockService
{
    protected $templates;

    /**
     * @return mixed
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @param mixed $templates
     */
    public function setTemplates($templates = array())
    {
        $this->templates = $templates;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bruery_media_block';
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        if (!$block->getSetting('mediaId') instanceof MediaInterface) {
            $this->load($block);
        }

        $formatChoices = $this->getFormatChoices($block->getSetting('mediaId'));

        $keys[] = array('title', 'text', array('required' => false));
        $keys[] = array($this->getMediaBuilder($formMapper), null, array());
        $keys[] = array('format', 'choice', array('required' => count($formatChoices) > 0, 'choices' => $formatChoices));
        if ($this->getTemplates()) {
            $keys[] = array('template', 'choice', array('choices'=>$this->getTemplates()));
        }

        $formMapper->add('settings', 'sonata_type_immutable_array', array('keys' => $keys, 'attr'=>array('class'=>'bruery-immutable-container')));
    }

    /**
     * @param ErrorElement   $errorElement
     * @param BlockInterface $block
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        $errorElement
            ->with('settings[mediaId]')
                ->addConstraint(new NotBlank())
            ->end();
    }
}
