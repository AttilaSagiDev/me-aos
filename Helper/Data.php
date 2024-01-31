<?php
/**
 * Copyright © 2016 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Me\Aos\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Store config is extension enabled
     */
    const XML_PATH_ENABLED = 'aos/basic/active';

    /**
     * Check if enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
}
