<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\MediaBundle\Provider\GalleryHasMedia;

use Bruery\MediaBundle\Provider\BasePool;

class Pool extends BasePool
{
    /**
     * @param string $name
     * @param array $provider
     * @param null $defaultTemplate
     * @param array $templates
     *
     * @return void
     */
    public function addCollection($name, $provider = null, $settings = null)
    {
        if ($this->slugify) {
            $name = $this->slugify->slugify($name);
        }

        if (!$this->hasGroup($name)) {
            $this->groups[$name] = array('provider' => null);
        }

        $this->groups[$name]['provider'] = $provider;
        $this->groups[$name]['settings'] = $settings;
    }

    public function getSettingsByCollection($name)
    {
        $group = $this->getGroup($name);

        if (!$group) {
            return null;
        }

        return $group['settings'];
    }
}
