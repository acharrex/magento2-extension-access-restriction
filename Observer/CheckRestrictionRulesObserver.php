<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\AccessRestriction\Observer;

use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Shopigo\AccessRestriction\Helper\Data as DataHelper;

class CheckRestrictionRulesObserver implements ObserverInterface
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * @param DataHelper $dataHelper
     * @param AppState $appState
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        DataHelper $dataHelper,
        AppState $appState,
        UrlInterface $urlInterface
    ) {
        $this->dataHelper = $dataHelper;
        $this->appState = $appState;
        $this->urlInterface = $urlInterface;
    }

    /**
     * Check restriction rules
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if ($this->appState->getAreaCode() == Area::AREA_ADMINHTML) {
            return $this;
        }
        if (!$this->dataHelper->isEnabled()) {
            return $this;
        }

        $event      = $observer->getEvent();
        $controller = $event->getControllerAction();
        $request    = $controller->getRequest();

        $rules = $this->dataHelper->getConfigValue();
        if (is_array($rules) && count($rules) > 0) {
            foreach ($rules as $rule) {
                if ($rule['module'] != $request->getModuleName()) {
                    continue;
                }
                if ($rule['controller'] != $request->getControllerName() && $rule['controller'] != '*') {
                    continue;
                }
                if ($rule['action'] != $request->getActionName() && $rule['action'] != '*'
                    &&  $rule['controller'] != '*'
                ) {
                    continue;
                }

                throw new NotFoundException(__('Page not found.'));
            }
        }
        return $this;
    }
}
