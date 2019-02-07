<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api;

/**
 * Language Mapper interface.
 * Converts current Magento locale into language code supported by QliroOne order
 *
 * @api
 */
interface LanguageMapperInterface
{
    /**
     * Get a prepared string that contains a QliroOne compatible language
     *
     * @return string
     */
    public function getLanguage();
}
