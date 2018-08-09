<?php
/**
 * This file is part of the Bruery Platform.
 *
 * (c) Viktore Zara <viktore.zara@gmail.com>
 * (c) Mell Zamorw <mellzamora@outlook.com>
 *
 * Copyright (c) 2016. For the full copyright and license information, please view the LICENSE  file that was distributed with this source code.
 */

namespace Bruery\EntityAuditBundle\Model;

use SimpleThings\EntityAudit\Revision as BaseRevision;

/**
 * Revision is returned from {@link AuditReader::getRevisions()}
 */
class Revision extends BaseRevision
{
    protected $rev;
    protected $timestamp;
    protected $username;

    public function __construct($rev, $timestamp, $username)
    {
        $this->rev = $rev;
        $this->timestamp = $timestamp;
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getRev()
    {
        return $this->rev;
    }

    /**
     * @param mixed $rev
     */
    public function setRev($rev)
    {
        $this->rev = $rev;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }
}
