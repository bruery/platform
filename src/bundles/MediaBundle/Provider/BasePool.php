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

use Bruery\CoreBundle\Provider\BasePool as Pool;

abstract class BasePool extends Pool
{
    public function addCollection($name, $provider = null)
    {
        if ($this->slugify) {
            $name = $this->slugify->slugify($name);
        }
        $this->addGroup($name, $provider);
    }

    public function hasCollection($name)
    {
        return $this->hasGroup($name);
    }

    public function getCollection($name)
    {
        return $this->getGroup($name);
    }

    public function getCollections()
    {
        return $this->getGroups();
    }

    public function getDefaultCollection()
    {
        return $this->getDefaultGroup();
    }

    public function getProviderNameByCollection($name)
    {
        return $this->getProviderNameByGroup($name);
    }
}
