<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Usage;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use BillaBear\Entity\Usage\Event;
use BillaBear\Pricing\Usage\Messenger\Handler\UpdateCustomerCountersHandler;
use BillaBear\Pricing\Usage\Messenger\Message\UpdateCustomerCounters;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Repository\Orm\EventRepository as OrmEventRepository;
use BillaBear\Repository\Orm\MetricRepository;
use BillaBear\Repository\Orm\SubscriptionPlanRepository;
use BillaBear\Repository\Orm\SubscriptionRepository;
use BillaBear\Repository\Usage\EventRepository;
use BillaBear\Tests\Behat\Customers\CustomerTrait;
use BillaBear\Tests\Behat\SendRequestTrait;
use BillaBear\Tests\Behat\SubscriptionPlan\SubscriptionPlanTrait;
use BillaBear\Tests\Behat\Subscriptions\SubscriptionTrait;
use Ramsey\Uuid\Uuid;

class EventContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;
    use SubscriptionTrait;
    use SubscriptionPlanTrait;
    use MetricTrait;

    public function __construct(
        private Session $session,
        private OrmEventRepository $ormEventRepository,
        private EventRepository $eventRepository,
        private CustomerRepository $customerRepository,
        private SubscriptionPlanRepository $planRepository,
        private SubscriptionRepository $subscriptionRepository,
        private MetricRepository $metricRepository,
        private UpdateCustomerCountersHandler $customerCountersHandler,
    ) {
    }

    /**
     * @When I create an event for customer :arg1 for subscription for :arg2 and metric :arg3 with the value :arg4
     */
    public function iCreateAnEventForCustomerForSubscriptionForWithTheValue($customerEmail, $planName, $metricName, $value)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $subscription = $this->getSubscription($customerEmail, $planName);
        $metric = $this->getMetric($metricName);

        $payload = [
            'event_id' => bin2hex(random_bytes(16)),
            'customer' => (string) $customer->getId(),
            'subscription' => (string) $subscription->getId(),
            'code' => $metric->getCode(),
            'properties' => [],
            'value' => floatval($value),
        ];

        $this->sendJsonRequest('POST', '/api/v1/events', $payload);
    }

    /**
     * @When I create an event with properties for customer :arg1 for subscription for :arg2 and metric :arg3 with the value :arg4
     */
    public function iCreateAnEventWithPropertiesForCustomerForSubscriptionForWithTheValue($customerEmail, $planName, $metricName, $value)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $subscription = $this->getSubscription($customerEmail, $planName);
        $metric = $this->getMetric($metricName);

        $payload = [
            'event_id' => bin2hex(random_bytes(16)),
            'customer' => (string) $customer->getId(),
            'subscription' => (string) $subscription->getId(),
            'code' => $metric->getCode(),
            'properties' => ['test_example' => 'value'],
            'value' => floatval($value),
        ];

        $this->sendJsonRequest('POST', '/api/v1/events', $payload);
    }

    /**
     * @Then there should be an event for customer :arg1
     */
    public function thereShouldBeAnEventForCustomer($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $result = $this->ormEventRepository->count(['customer' => $customer]);
        if (1 !== $result) {
            throw new \Exception('Expected to find 1 but got '.(string) $result);
        }
    }

    /**
     * @Given the following events exist:
     */
    public function theFollowingEventsExist(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $customer = $this->getCustomerByEmail($row['Customer']);
            $metric = $this->getMetric($row['Metric']);
            $subscription = $this->getSubscription($row['Customer'], $row['Subscription Plan']);
            $count = intval($row['Repeated'] ?? 1);

            for ($i = 0; $i < $count; ++$i) {
                $properties = $row['Properties'] ?? '[]';
                $properties = str_replace('%d', random_int(1, 1000), $properties);

                $event = new Event();
                $event->setId(Uuid::uuid4());
                $event->setEventId(bin2hex(random_bytes(16)));
                $event->setCustomer($customer);
                $event->setMetric($metric);
                $event->setSubscription($subscription);
                $event->setValue(floatval($row['Value']));
                $event->setCreatedAt(new \DateTime($row['Created At'] ?? $count - $i.' hours'));
                $event->setProperties(json_decode($properties, true));

                $this->eventRepository->save($event);
            }
        }
    }

    /**
     * @When the background task to update customer events is ran for :customerEmail
     */
    public function theBackgroundTaskToUpdateCustomerEventsIsRan(string $customerEmail): void
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $message = new UpdateCustomerCounters((string) $customer->getId());
        $this->customerCountersHandler->__invoke($message);
    }
}
