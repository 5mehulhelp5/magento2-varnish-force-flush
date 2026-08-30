<?php
declare(strict_types=1);

namespace Nx6\VarnishPurge\Controller\Adminhtml\Cache;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Nx6\VarnishPurge\Model\VarnishPurger;

class Purge extends Action implements HttpGetActionInterface
{
    public const string ADMIN_RESOURCE = 'Nx6_VarnishPurge::varnish_purge';

    public function __construct(
        Context $context,
        private readonly VarnishPurger $varnishPurger,
    ) {
        parent::__construct($context);
    }

    #[\Override]
    public function execute()
    {
        $varnishPurgeResult = $this->varnishPurger->purge();

        if ($varnishPurgeResult->success) {
            $this->messageManager->addSuccessMessage($varnishPurgeResult->message);
        } else {
            $this->messageManager->addErrorMessage($varnishPurgeResult->message);
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('adminhtml/cache');
    }
}
