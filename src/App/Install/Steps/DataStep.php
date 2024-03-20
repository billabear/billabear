<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Install\Steps;

use App\Background\Payments\ExchangeRatesFetchProcess;
use App\Entity\TaxType;
use App\Kernel;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\TaxTypeRepositoryInterface;
use Http\Discovery\Psr18ClientDiscovery;
use Nyholm\Psr7\Request;
use Stripe\Account;
use Stripe\Balance;
use Stripe\Payout;
use Stripe\Stripe;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class DataStep
{
    public function __construct(
        private ExchangeRatesFetchProcess $ratesFetchProcess,
        private SettingsRepositoryInterface $settingsRepository,
        #[Autowire('%parthenon_billing_payments_obol_config%')]
        private $stripeConfig,
        private TaxTypeRepositoryInterface $taxTypeRepository,
    ) {
    }

    public function install()
    {
        $this->announceInstall();
        $this->createTaxTypes();
        $this->ratesFetchProcess->process();
    }

    private function createTaxTypes(): void
    {
        $taxType = new TaxType();
        $taxType->setName('Digital');
        $taxType->setPhysical(false);
        $taxType->setDefault(true);

        $this->taxTypeRepository->save($taxType);
        $taxType = new TaxType();

        $taxType->setName('Physical');
        $taxType->setPhysical(true);
        $taxType->setDefault(false);

        $this->taxTypeRepository->save($taxType);

        $taxType = new TaxType();
        $taxType->setName('Digital Services');
        $taxType->setPhysical(false);
        $taxType->setDefault(false);

        $this->taxTypeRepository->save($taxType);
    }

    private function announceInstall()
    {
        // Data collected for license enforcement.
        Stripe::setApiKey($this->stripeConfig['api_key']);
        $balance = Balance::retrieve();
        $account = Account::retrieve();
        $dateTime = new \DateTime('-45 days');
        $payoutsData = Payout::all(['limit' => 35, 'created' => ['gt' => $dateTime->getTimestamp()]]);

        $totalAmount = 0;

        /** @var Payout $payout */
        foreach ($payoutsData->data as $payout) {
            $totalAmount += $payout->amount;
        }
        $balancePending = 0;
        foreach ($balance->pending as $pending) {
            $balancePending += $pending->amount;
        }

        $payload = [
            'stripe_account_id' => $account->id,
            'stripe_dashboard_display_name' => $account->settings->dashboard->display_name,
            'stripe_statement_descriptor' => $account->settings->card_payments->statement_descriptor_prefix,
            'stripe_account_type' => $account->type,
            'stripe_payout' => $totalAmount,
            'stripe_currency' => $account->default_currency,
            'stripe_country' => $account->country,
            'stripe_balance' => $balancePending,
            'stripe_livemode' => $balance->livemode,
        ];

        if (isset($account->email)) {
            $payload['stripe_owner_email'] = $account->email;
        }

        if (isset($account->business_profile)) {
            $payload['stripe_owner_name'] = $account->business_profile->name;
            $payload['stripe_support_email'] = $account->business_profile->support_email;
        }
        $settings = $this->settingsRepository->getDefaultSettings();
        $payload['url'] = $settings->getSystemSettings()->getSystemUrl();
        $payload['id'] = $settings->getId();
        $payload['version'] = Kernel::VERSION;

        $request = new Request('POST', 'https://announce.billabear.com/install', headers: ['Content-Type' => 'application/json'], body: json_encode($payload));

        $client = Psr18ClientDiscovery::find();
        $client->sendRequest($request);
    }
}
