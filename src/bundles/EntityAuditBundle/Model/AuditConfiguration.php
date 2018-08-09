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

use SimpleThings\EntityAudit\AuditConfiguration as BaseAuditConfiguration;

class AuditConfiguration extends BaseAuditConfiguration
{
    protected $sourceEm;
    protected $auditEm;

    /**
     * @return mixed
     */
    public function getAuditEm()
    {
        return $this->auditEm;
    }

    /**
     * @param mixed $auditEm
     */
    public function setAuditEm($auditEm)
    {
        $this->auditEm = $auditEm;
    }

    /**
     * @return mixed
     */
    public function getSourceEm()
    {
        return $this->sourceEm;
    }

    /**
     * @param mixed $sourceEm
     */
    public function setSourceEm($sourceEm)
    {
        $this->sourceEm = $sourceEm;
    }
}
