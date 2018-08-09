<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\MediaBundle\Provider;

use Bruery\CoreBundle\Provider\BaseProvider as Provider;
use Sonata\AdminBundle\Form\FormMapper;

abstract class BaseProvider extends Provider
{
    public function getFormSettingsKeys(FormMapper $formMapper)
    {
    }

    public function buildCreateForm(FormMapper $formMapper)
    {
    }

    public function buildEditForm(FormMapper $formMapper)
    {
    }
}
