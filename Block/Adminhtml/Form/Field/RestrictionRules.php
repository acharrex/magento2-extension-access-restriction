<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Shopigo\AccessRestriction\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class RestrictionRules extends AbstractFieldArray
{
    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'module',
            [
                'label'    => __('Module')
            ]
        );

        $this->addColumn(
            'controller',
            [
                'label'    => __('Controller')
            ]
        );

        $this->addColumn(
            'action',
            [
                'label'    => __('Action')
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Rule');
    }
}
