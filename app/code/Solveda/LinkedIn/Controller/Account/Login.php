<?php
namespace Solveda\LinkedIn\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Login extends Action
{
    protected $customerSession;

    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository
        )
    {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    public function execute()
    {
        // LinkedIn authentication

        $userData = $this->getLinkedInUserData();

        $customerId = $this->getCustomerIdByEmail($userData['emailAddress']);

        if ($customerId) {
            $this->customerSession->setCustomerAsLoggedIn($customerId);
        } else {
            $customerId = $this->createCustomerAccount($userData);
            $this->customerSession->setCustomerAsLoggedIn($customerId);
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('/');
        return $resultRedirect;
    }

    protected function getLinkedInUserData()
    {
        //
        return $userData;
    }

    protected function getCustomerIdByEmail($email)
    {
        $customerId = null;
        try {
            $customerData = $this->customerRepository->get($email);
            $customerId = (int)$customerData->getId();
        }catch (NoSuchEntityException $noSuchEntityException){
        }
        return $customerId;
    }

    protected function createCustomerAccount($userData)
    {

        //
        
        return $customerId;
    }
}
