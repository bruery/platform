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

use Sonata\ClassificationBundle\Entity\CollectionManager as BaseCollectionManager;
use Sonata\ClassificationBundle\Model\ContextInterface;

class CollectionManager extends BaseCollectionManager
{

    protected $slugify;

    /**
     * @return mixed
     */
    public function getSlugify()
    {
        return $this->slugify;
    }

    /**
     * @param mixed $slugify
     */
    public function setSlugify($slugify)
    {
        $this->slugify = $slugify;
    }

    public function generateDefaultCollection(ContextInterface $context, $slug, $enabled = true)
    {
        $slug = $this->getSlugify()->slugify($slug);
        $name = ucwords(str_replace('-', ' ', $slug));
        //create collection
        $collection = $this->create();
        $collection->setContext($context);
        $collection->setName($name);
        $collection->setDescription($name);
        $collection->setEnabled($enabled);
        $this->save($collection);
        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function findByContext($context, $enabled = true)
    {
        $query = $this->getRepository()
            ->createQueryBuilder('c')
            ->select('c')
            ->where('c.context = :context')
            ->andWhere('c.enabled = :enabled')
            ->setParameters(array('context'=>$context, 'enabled'=>$enabled))
            ->getQuery()
            ->useQueryCache(true, 3600)
            ->useResultCache(true, 3600);

        try {
            return $query->getResult();
        } catch (\Exception $e) {
            throw $e;
            return false;
        }
    }
}
