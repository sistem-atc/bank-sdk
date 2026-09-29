<?php

declare(strict_types=1);

namespace SistemAtc\Banks\PagBrasil\DTO\Request\Checkout;

use DateTimeInterface;
use InvalidArgumentException;
use SistemAtc\Banks\Common\Attributes\ArrayOf;
use SistemAtc\Banks\Common\Attributes\JsonKey;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Flatten;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Ignore;
use SistemAtc\Banks\PagBrasil\DTO\Request\Attributes\Money;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Address;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\AutomaticPixTerms;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Customer;
use SistemAtc\Banks\PagBrasil\DTO\Request\Common\Product;
use SistemAtc\Banks\PagBrasil\DTO\Request\RequestPayload;
use SistemAtc\Banks\PagBrasil\DTO\Request\SerializesRequest;
use SistemAtc\Banks\PagBrasil\Enums\CaptureMode;
use SistemAtc\Banks\PagBrasil\Enums\PaymentMethod;

/**
 * Link de Pagamento (/api/checkout/add). Só `order` e `amountBrl` são
 * obrigatórios; o que faltar de dado do cliente o checkout pede na tela.
 *
 *   - paymentOptions: meios exibidos (C, D, B, X); vazio = todos os da conta.
 *   - walletOptions: 'AP', 'GP', 'SP'.
 *   - expirationDays OU expirationDate.
 *   - requestAddress/Phone/Email = false pra NÃO pedir o dado no checkout.
 *   - require3ds*: usar 3DS no crédito/débito; *OnFailure = seguir sem
 *     autenticação (true) ou recusar (false) quando o 3DS falhar.
 *   - fxCurrency + fxAmount: preço em USD/EUR (conta com câmbio garantido).
 *   - automaticPix: checkout vira pagamento + consentimento Pix Automático.
 */
final class PaymentLink implements RequestPayload
{
    use SerializesRequest;

    /**
     * @param  list<PaymentMethod>  $paymentOptions
     * @param  list<string>  $walletOptions
     * @param  list<Product>  $products
     */
    public function __construct(
        public readonly string $order,
        #[Money]
        public readonly int|float|string $amountBrl,
        #[Ignore]
        public readonly array $paymentOptions = [],
        #[Ignore]
        public readonly array $walletOptions = [],
        #[JsonKey('payment_link_expiration')]
        public readonly ?int $expirationDays = null,
        #[JsonKey('payment_link_expiration_date')]
        public readonly DateTimeInterface|string|null $expirationDate = null,
        public readonly ?string $urlReturn = null,
        public readonly ?string $productName = null,
        #[ArrayOf(Product::class)]
        public readonly array $products = [],
        #[Ignore]
        public readonly bool $requestAddress = true,
        #[Ignore]
        public readonly bool $requestPhone = true,
        #[Ignore]
        public readonly bool $requestEmail = true,
        #[Flatten]
        public readonly ?Customer $customer = null,
        #[Flatten]
        public readonly ?Address $address = null,
        public readonly ?string $paramUrl = null,
        public readonly ?string $softDescriptor = null,
        #[JsonKey('cc_auth')]
        public readonly ?CaptureMode $capture = null,
        #[JsonKey('cc_installments')]
        public readonly ?int $installments = null,
        public readonly ?int $maxInstallments = null,
        public readonly ?string $storeCode = null,
        #[JsonKey('bol_expiration')]
        public readonly ?int $boletoExpirationDays = null,
        #[JsonKey('pix_expiration')]
        public readonly ?int $pixExpirationMinutes = null,
        #[Ignore]
        public readonly ?bool $require3dsCredit = null,
        #[Ignore]
        public readonly ?bool $proceedOnCredit3dsFailure = null,
        #[Ignore]
        public readonly ?bool $require3dsDebit = null,
        #[Ignore]
        public readonly ?bool $proceedOnDebit3dsFailure = null,
        public readonly ?string $fxCurrency = null,
        #[Money]
        public readonly int|float|string|null $fxAmount = null,
        #[Flatten]
        public readonly ?AutomaticPixTerms $automaticPix = null,
    ) {
        if ($expirationDays !== null && $expirationDate !== null) {
            throw new InvalidArgumentException('PagBrasil: use expirationDays OU expirationDate.');
        }

        if (($fxCurrency === null) !== ($fxAmount === null)) {
            throw new InvalidArgumentException('PagBrasil: fxCurrency e fxAmount andam juntos.');
        }

        if ($capture === CaptureMode::Capture) {
            throw new InvalidArgumentException('PagBrasil: o Link de Pagamento só autoriza+captura ou pré-autoriza.');
        }
    }

    protected function fixedFields(): array
    {
        $fields = [];

        if ($this->paymentOptions !== []) {
            $fields['payment_option'] = implode(',', array_map(fn (PaymentMethod $m) => $m->value, $this->paymentOptions));
        }

        if ($this->walletOptions !== []) {
            $fields['wallet_option'] = implode(',', $this->walletOptions);
        }

        // Default da API = pedir o dado; só vale mandar quando é "não pedir".
        foreach (['address_requested' => $this->requestAddress, 'phone_requested' => $this->requestPhone, 'email_requested' => $this->requestEmail] as $key => $request) {
            if (! $request) {
                $fields[$key] = '0';
            }
        }

        // 3DS: "2" = usar / seguir; "1" = não usar / recusar.
        foreach ([
            'cc_authentication' => $this->require3dsCredit,
            'cc_authentication_onfailure' => $this->proceedOnCredit3dsFailure,
            'dc_authentication' => $this->require3dsDebit,
            'dc_authentication_onfailure' => $this->proceedOnDebit3dsFailure,
        ] as $key => $flag) {
            if ($flag !== null) {
                $fields[$key] = $flag ? '2' : '1';
            }
        }

        if ($this->automaticPix !== null) {
            $fields['pix_rec'] = '1';
        }

        return $fields;
    }
}
