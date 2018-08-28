<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Shopigo\AccessRestriction\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Math\Random as MathRandom;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Config paths
     */
    const XML_PATH_ENABLED           = 'general/shopigo_access_restriction/enabled';
    const XML_PATH_RESTRICTION_RULES = 'general/shopigo_access_restriction/rules';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var MathRandom
     */
    protected $mathRandom;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param MathRandom $mathRandom
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        MathRandom $mathRandom
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->mathRandom = $mathRandom;
    }

    /**
     * Generate a storable representation of a value
     *
     * @param int|float|string|array $value
     * @return string
     */
    protected function serializeValue($value)
    {
        if (is_array($value)) {
            return serialize($value);
        } else {
            return '';
        }
    }

    /**
     * Create a value from a storable representation
     *
     * @param int|float|string $value
     * @return array
     */
    protected function unserializeValue($value)
    {
        if (is_string($value) && !empty($value)) {
            return unserialize($value);
        } else {
            return [];
        }
    }

    /**
     * Check whether value is in form retrieved by _encodeArrayFieldValue()
     *
     * @param string|array $value
     * @return bool
     */
    protected function isEncodedArrayFieldValue($value)
    {
        if (!is_array($value)) {
            return false;
        }
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('module', $row)
                || !array_key_exists('controller', $row)
                || !array_key_exists('action', $row)
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];
        foreach ($value as $row) {
            $resultId = $this->mathRandom->getUniqueHash('_');
            $result[$resultId] = $row;
        }
        return $result;
    }

    /**
     * Decode value from used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function decodeArrayFieldValue(array $value)
    {
        $result = [];
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('module', $row)
                || !array_key_exists('controller', $row)
                || !array_key_exists('action', $row)
            ) {
                continue;
            }
            $result[] = $row;
        }
        return $result;
    }

    /**
     * Check if the module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (!$this->isModuleOutputEnabled()) {
            return false;
        }
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve restriction rules from config
     *
     * @return array|null
     */
    public function getConfigValue()
    {
        $value = $this->scopeConfig->getValue(self::XML_PATH_RESTRICTION_RULES);
        if (empty($value)) {
            return null;
        }

        $value = $this->unserializeValue($value);
        if ($this->isEncodedArrayFieldValue($value)) {
            $value = $this->decodeArrayFieldValue($value);
        }

        return $value;
    }

    /**
     * Make value readable by \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param string|array $value
     * @return array
     */
    public function makeArrayFieldValue($value)
    {
        $value = $this->unserializeValue($value);
        if (!$this->isEncodedArrayFieldValue($value)) {
            $value = $this->encodeArrayFieldValue($value);
        }
        return $value;
    }

    /**
     * Make value ready for store
     *
     * @param string|array $value
     * @return string
     */
    public function makeStorableArrayFieldValue($value)
    {
        if ($this->isEncodedArrayFieldValue($value)) {
            $value = $this->decodeArrayFieldValue($value);
        }
        $value = $this->serializeValue($value);
        return $value;
    }
}