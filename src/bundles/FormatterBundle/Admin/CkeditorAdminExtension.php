<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\FormatterBundle\Admin;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\FormatterBundle\Admin\CkeditorAdminExtension as AdminExtension;

/**
 * Adds browser and upload routes to the Admin.
 *
 * @author Kévin Dunglas <kevin@les-tilleuls.coop>
 */
class CkeditorAdminExtension extends AdminExtension
{
    /**
     * {@inheritdoc}
     */
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        $collection->add('ckeditor_browser', 'ckeditor_browser', array(
            '_controller' => 'BrueryFormatterBundle:CkeditorAdmin:browser',
        ));

        $collection->add('ckeditor_upload', 'ckeditor_upload', array(
            '_controller' => 'BrueryFormatterBundle:CkeditorAdmin:upload',
        ));
    }
}
