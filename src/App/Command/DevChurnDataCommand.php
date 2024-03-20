<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Command;

use App\Entity\CancellationRequest;
use App\Enum\CancellationType;
use App\Repository\CancellationRequestRepositoryInterface;
use App\Repository\SubscriptionRepositoryInterface;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:dev:churn-data', description: 'Generate some churn data')]
class DevChurnDataCommand extends Command
{
    public function __construct(
        protected CancellationRequestRepositoryInterface $cancellationRequestRepository,
        protected SubscriptionRepositoryInterface $subscriptionRepository,
    ) {
        parent::__construct(null);
    }

    protected function configure()
    {
        $this->addOption('date', mode: InputOption::VALUE_REQUIRED, description: 'The starting start churning', default: '-6 months')
            ->addOption('count', mode: InputOption::VALUE_REQUIRED, description: 'The number of users to add', default: 4);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Generating churn data');
        $count = $input->getOption('count');
        $date = new \DateTime($input->getOption('date'));
        $now = new \DateTime();

        $buffer = intval($count / 2);
        $lower = $count - $buffer;
        $upper = $count + $buffer;

        while ($date <= $now) {
            $roundCount = random_int($lower, $upper);
            $subscriptions = $this->subscriptionRepository->findActiveSubscriptionsOnDate($date, $roundCount);

            foreach ($subscriptions as $subscription) {
                $cancelRequest = new CancellationRequest();
                $cancelRequest->setSubscription($subscription);
                $cancelRequest->setCreatedAt($date);
                $cancelRequest->setWhen('when');
                $cancelRequest->setRefundType('none');
                $cancelRequest->setState('started');
                $cancelRequest->setOriginalValidUntil($subscription->getValidUntil());
                $cancelRequest->setCancellationType(CancellationType::CUSTOMER_REQUEST);

                $this->cancellationRequestRepository->save($cancelRequest);

                $subscription->setStatus(SubscriptionStatus::PENDING_CANCEL);
                $this->subscriptionRepository->save($subscription);
            }

            $date->modify('+1 day');
        }

        return Command::SUCCESS;
    }
}
