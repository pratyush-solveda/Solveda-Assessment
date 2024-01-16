<?php
namespace Solveda\LinkedIn\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Solveda\LinkedIn\Model\LinkedInData;

class Login extends Action
{
    protected $customerSession;
    protected $_linkedInData;

    public function __construct(
        Context $context,
        Session $customerSession,
        LinkedInData $linkedInData,
        CustomerRepositoryInterface $customerRepository
        )
    {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->_linkedInData = $linkedInData;
        $this->customerRepository = $customerRepository;
    }

    public function execute()
    {
        try {
            $userData = $this->getLinkedInUserData();

            if (!$userData) {
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('/');
                return $resultRedirect;
            }

            $customerId = $this->getCustomerIdByEmail($userData['emailAddress']);

            if ($customerId) {
                $this->customerSession->setCustomerAsLoggedIn($customerId);
            } else {
                $customerId = $this->createCustomerAccount($userData);

                if ($customerId) {
                    $this->customerSession->setCustomerAsLoggedIn($customerId);
                } else {
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setPath('/');
                    return $resultRedirect;
                }
            }

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('/');
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('An error occurred during LinkedIn login.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }
    }


    protected function getLinkedInUserData()
    {
        try {
            $code = $this->getRequest()->getParam('code');
            $accessToken = $this->_linkedInData->getAccessToken($code);
            $userData = $this->_linkedInData->getUserData($accessToken);

            return json_decode($userData, true);
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Error during LinkedIn authentication.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }
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
        if (!isset($userData['emailAddress'])) {
            $this->messageManager->addError(__('LinkedIn did not provide an email address.'));
            return null;
        }

        $customer = $this->customerRepository->create();
        $customer->setEmail($userData['emailAddress']);
        $customer->setFirstname($userData['localizedFirstName']);
        $customer->setLastname($userData['localizedLastName']);
        $customer->setGender($userData['gender'] ?? '');

        try {
            $customer = $this->customerRepository->save($customer);
            return $customer->getId();
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Could not create customer account.'));
            return null;
        }
    }

}
