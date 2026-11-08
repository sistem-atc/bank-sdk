<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\DTO\Request\AccountInfo\DeleteWhiteLabelRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Common\AccountStatus;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountInfo\RetrieveWalletIdResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\AccountInfo\UpdateBusinessDataRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountInfo\RetrieveAccountFeesResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountInfo\RetrieveBusinessDataResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\AccountInfo\UpdateCheckoutCustomizationRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountInfo\DeleteWhiteLabelSubaccountResponseDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\AccountInfo\RetrieveAsaasAccountNumberResponseDTO;

class AccountInfo extends BaseMethods
{

    public function retrieveBusinessData(): RetrieveBusinessDataResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/myAccount/commercialInfo/');
        return RetrieveBusinessDataResponseDTO::fromArray($response);
    }

    public function updateBusinessData(UpdateBusinessDataRequestDTO $data): RetrieveBusinessDataResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/myAccount/commercialInfo/', $data);
        return RetrieveBusinessDataResponseDTO::fromArray($response);
    }

    public function savePaymentCheckoutCustomization(UpdateCheckoutCustomizationRequestDTO $data): AccountStatus
    {
        $response = $this->makeRequest(HttpMethod::POST, '/myAccount/paymentCheckoutConfig/', $data);
        return AccountStatus::fromArray($response);
    }

    public function retrievePersonalizationSettings(): AccountStatus
    {
        $response = $this->makeRequest(HttpMethod::GET, '/myAccount/paymentCheckoutConfig/');
        return AccountStatus::fromArray($response);
    }

    public function retrieveAsaasAccountNumber(): RetrieveAsaasAccountNumberResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/myAccount/accountNumber');
        return RetrieveAsaasAccountNumberResponseDTO::fromArray($response);
    }

    public function retrieveAccountFees(): RetrieveAccountFeesResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/myAccount/fees/');
        return RetrieveAccountFeesResponseDTO::fromArray($response);
    }

    public function checkAccountStatus(): AccountStatus
    {
        $response = $this->makeRequest(HttpMethod::GET, '/myAccount/status/');
        return AccountStatus::fromArray($response);
    }

    public function retrieveWalletId(): RetrieveWalletIdResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/myAccount/walletId/');
        return RetrieveWalletIdResponseDTO::fromArray($response);
    }

    public function deleteWhiteLabelSubaccount(DeleteWhiteLabelRequestDTO $data): DeleteWhiteLabelSubaccountResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::DELETE, '/myAccount', $data);
        return DeleteWhiteLabelSubaccountResponseDTO::fromArray($response);
    }
}