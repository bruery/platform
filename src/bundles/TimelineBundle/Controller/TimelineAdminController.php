<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\TimelineBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Spy\Timeline\Model\TimelineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TimelineAdminController extends CRUDController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function timelineAction(Request $request = null)
    {
        $token = $this->get('security.token_storage')->getToken();
        if (!$token) {
            throw $this->createNotFoundException('No User Token');
        }

        $subject = $this->get('spy_timeline.action_manager')->findOrCreateComponent($token->getUser(), $token->getUser()->getId());


        $entries = $this->get('spy_timeline.timeline_manager')->getTimeline($subject, array(
          'page'            => $request->query->get('page') ?: 1,
          'max_per_page'    => 25,
          'type'            => TimelineInterface::TYPE_TIMELINE,
          'context'         => 'SONATA_ADMIN',
          'filter'          => true,
          'group_by_action' => true,
          'paginate'        => true
        ));


        if ($request->isXmlHttpRequest()) {
            $content = $this->render('BrueryTimelineBundle:TimelineAdmin:timeline_ajax.html.twig', array(
                                     'entries'    => $entries,
                                     'admin_pool' => $this->get('sonata.admin.pool')));

            $loadMore = $this->render('BrueryTimelineBundle:TimelineAdmin:timeline_load_more.html.twig', array(
                                      'entries'    => $entries,
                                      'admin_pool' => $this->get('sonata.admin.pool')));

            return new JsonResponse(array('status' => 'OK', 'content' => $content->getContent(), 'loadMore'=>$loadMore->getContent()));
        } else {
            return $this->render('BrueryTimelineBundle:TimelineAdmin:timeline.html.twig', array(
                'action'     => 'timeline',
                'entries'    => $entries,
                'admin_pool' => $this->get('sonata.admin.pool')
            ));
        }
    }
}
