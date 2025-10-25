<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Xtreme\BulkDeleteCouponCodes\Controller\Adminhtml\Actions;

use Magento\SalesRule\Model\RuleFactory;
use Magento\Backend\App\Action as BackendAction;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * PromoQuote MassRemove controller
 */
class MassRemove extends BackendAction implements HttpPostActionInterface
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Xtreme_BulkDeleteCouponCodes::promoquote_remove';

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @param BackendAction\Context $context
     * @param RuleFactory $couponFactory
     */
    public function __construct(BackendAction\Context $context, RuleFactory $ruleFactory)
    {
        parent::__construct($context);
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('promoquote');
        if (!is_array($ids)) {
            $this->messageManager->addErrorMessage(__('Please select cart rules.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = $this->ruleFactory->create()->load($id);
                    if ($model->getId()) {
                        $model->delete();
                    }
                }
                $this->messageManager->addSuccessMessage(__('Total of %1 record(s) have been removed.', count($ids)));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __("We couldn't remove the messages because of an error.")
                );
            }
        }
        return $this->_redirect('sales_rule/promo_quote/index');
    }
}
