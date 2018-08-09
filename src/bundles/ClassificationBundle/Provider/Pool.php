<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\ClassificationBundle\Provider;

use Bruery\CoreBundle\Provider\BasePool;

abstract class Pool extends BasePool
{
    public function addContext($name, $provider = null)
    {
        $this->addGroup($name, $provider);
    }

    public function hasContext($name)
    {
        return $this->hasGroup($name);
    }

    public function getContext($name)
    {
        return $this->getGroup($name);
    }

    public function getContexts()
    {
        return $this->getGroups();
    }

    public function getDefaultContext()
    {
        return $this->getDefaultGroup();
    }

    public function getProviderNameByContext($name)
    {
        return $this->getProviderNameByGroup($name);
    }
}
