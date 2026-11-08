<?php

namespace SistemAtc\Banks\Gateways\Asaas\Methods;

use SistemAtc\Banks\Gateways\Asaas\Enums\HttpMethod;
use SistemAtc\Banks\Gateways\Asaas\Bases\BaseMethods;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Notification\NotificationRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Shared\Response\Notification as NotificationResponse;
use SistemAtc\Banks\Gateways\Asaas\DTO\Request\Notification\UpdateNotificationBatchRequestDTO;
use SistemAtc\Banks\Gateways\Asaas\DTO\Response\Notification\UpdateNotificationBatchResponseDTO;

class Notification extends BaseMethods
{
    public function updateExistingNotification(string $id, NotificationRequestDTO $data): NotificationResponse
    {
        $response = $this->makeRequest(HttpMethod::PUT,"/notifications/{$id}",$data);
        return NotificationResponse::fromArray($response);
    }
    
    public function updateExistingNotificationsinBatch(UpdateNotificationBatchRequestDTO $data): UpdateNotificationBatchResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::PUT,"/notifications/batch",$data);
        return UpdateNotificationBatchResponseDTO::fromArray($response);
    }
}