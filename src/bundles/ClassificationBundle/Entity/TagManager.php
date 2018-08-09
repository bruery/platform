<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamora <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\TagManager as BaseTagManager;

class TagManager extends BaseTagManager
{
    public function geTagQueryForDatagrid($contexts)
    {
        $qb = $this->getRepository()->createQueryBuilder('t');
        $qb->select('t')
            ->andWhere($qb->expr()->in('t.context', $contexts));

        return $qb->getQuery();
    }
}
