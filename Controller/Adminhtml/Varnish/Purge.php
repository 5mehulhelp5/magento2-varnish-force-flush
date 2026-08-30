<?php
declare(strict_types=1);

namespace Nx6\VarnishPurge\Controller\Adminhtml\Varnish;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Nx6\VarnishPurge\Model\VarnishPurger;

class Purge extends Action
{
    public const string ADMIN_RESOURCE = 'Nx6_VarnishPurge::varnish_purge';

    public function __construct(
        Context $context,
        private readonly VarnishPurger $varnishPurger,
        private readonly JsonFactory $jsonFactory,
    ) {
        parent::__construct($context);
    }

    #[\Override]
    public function execute()
    {
        $varnishPurgeResult = $this->varnishPurger->purge();

        return $this->jsonFactory->create()->setData([
            'success' => $varnishPurgeResult->success,
            'message' => $varnishPurgeResult->message,
        ]);
    }
}
