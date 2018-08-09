<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\TimelineBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\TimelineBundle\Block\TimelineBlock;
use Spy\Timeline\Model\TimelineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class TimelineBlockService extends TimelineBlock
{
    /**
     * @var SecurityContextInterface
     */
    protected $adminPool;

    /**
     * @return SecurityContextInterface
     */
    public function getAdminPool()
    {
        return $this->adminPool;
    }

    /**
     * @param SecurityContextInterface $adminPool
     */
    public function setAdminPool($adminPool)
    {
        $this->adminPool = $adminPool;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $token = $this->securityContext->getToken();

        if (!$token) {
            return new Response();
        }

        $subject = $this->actionManager->findOrCreateComponent($token->getUser(), $token->getUser()->getId());

        $entries = $this->timelineManager->getTimeline($subject, array(
            'page'            => 1,
            'max_per_page'    => $blockContext->getSetting('max_per_page'),
            'type'            => TimelineInterface::TYPE_TIMELINE,
            'context'         => $blockContext->getSetting('context'),
            'filter'          => $blockContext->getSetting('filter'),
            'group_by_action' => $blockContext->getSetting('group_by_action'),
            'paginate'        => $blockContext->getSetting('paginate'),
        ));

        return $this->renderPrivateResponse($blockContext->getTemplate(), array(
            'context'  => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block'    => $blockContext->getBlock(),
            'entries'  => $entries,
            'admin_pool' => $this->adminPool
        ), $response);
    }
}
