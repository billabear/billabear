<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Workflow;

use App\Entity\WorkflowTransition;
use App\Enum\WorkflowType;
use App\Repository\Orm\WorkflowTransitionRepository;
use App\Tests\Behat\SendRequestTrait;
use App\Workflow\Places\PlaceInterface;
use App\Workflow\Places\SubscriptionCancel\Completed;
use App\Workflow\Places\SubscriptionCancel\CustomerNoticeSent;
use App\Workflow\Places\SubscriptionCancel\InternalNoticeSent;
use App\Workflow\Places\SubscriptionCancel\RefundIssued;
use App\Workflow\Places\SubscriptionCancel\Started;
use App\Workflow\Places\SubscriptionCancel\StatsGenerated;
use App\Workflow\Places\SubscriptionCancel\SubscriptionCancelled;
use App\Workflow\TransitionHandlers\WebhookTransitionHandler;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class CancellationRequestContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private WorkflowTransitionRepository $workflowTransitionRepository,
    ) {
    }

    /**
     * @When I go to the edit cancellation request workflow
     */
    public function iGoToTheEditCancellationRequestWorkflow()
    {
        $this->sendJsonRequest('GET', '/app/workflow/cancellation-request/edit');
    }

    /**
     * @Then I will see the hardcoded cancellation request places
     */
    public function iWillSeeTheHardcodedCancellationRequestPlaces()
    {
        $data = $this->getJsonContent();
        /** @var PlaceInterface[] $places */
        $places = [new Started(), new SubscriptionCancelled(), new RefundIssued(), new StatsGenerated(), new CustomerNoticeSent(), new InternalNoticeSent(), new Completed()];

        $alreadyFound = [];
        foreach ($data['places'] as $placeData) {
            foreach ($places as $place) {
                if (in_array($place->getName(), $alreadyFound)) {
                    continue;
                }
                if ($place->getName() == $placeData['name']) {
                    $alreadyFound[] = $place->getName();
                    break;
                }
            }
        }

        if (sizeof($places) !== count($alreadyFound)) {
            throw new \Exception(sprintf('Found %s instead of %s', json_encode($alreadyFound), json_encode(array_map(function (PlaceInterface $place) { return $place->getName(); }))));
        }
    }

    /**
     * @Given there are workflow transitions
     */
    public function thereAreWorkflowTransitions(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $entity = new WorkflowTransition();
            $entity->setName($row['Name']);
            $entity->setPriority(intval($row['Priority']));
            $entity->setEnabled(true);
            $entity->setWorkflow(WorkflowType::fromName($row['Workflow']));
            $entity->setHandlerName($row['Handler']);
            $entity->setHandlerOptions(json_decode($row['Options'] ?? '[]'));
            $entity->setCreatedAt(new \DateTime());
            $entity->setUpdatedAt(new \DateTime());
            $this->workflowTransitionRepository->getEntityManager()->persist($entity);
        }
        $this->workflowTransitionRepository->getEntityManager()->flush();
    }

    /**
     * @Then I will see the transition :arg1
     */
    public function iWillSeeTheTransition($transitionName)
    {
        $data = $this->getJsonContent();
        foreach ($data['places'] as $placeData) {
            if ($transitionName == $placeData['name']) {
                return;
            }
        }

        throw new \Exception('Did not find transition');
    }

    /**
     * @Then I will see the dynamic event handler for sending a webhook request
     */
    public function iWillSeeTheDynamicEventHandlerForSendingAWebhookRequest()
    {
        $data = $this->getJsonContent();

        foreach ($data['handlers'] as $handler) {
            if (WebhookTransitionHandler::NAME === $handler['name']) {
                return;
            }
        }

        throw new \Exception('Handler not found');
    }

    /**
     * @When I create a workflow transition for :arg1 with:
     */
    public function iCreateAWorkflowTransitionForWith($workflowName, TableNode $table)
    {
        $data = $table->getRowsHash();
        $this->sendJsonRequest('POST', '/app/workflow/create-transition', [
            'workflow' => $workflowName,
            'name' => $data['Name'],
            'priority' => intval($data['Priority']),
            'handler' => $data['Handler'],
            'handler_options' => json_decode($data['Handler Options']),
        ]);
    }

    /**
     * @Then there should be a transition called :arg1 for :arg2
     */
    public function thereShouldBeATransitionCalled($transitionName, $workflowName)
    {
        $entity = $this->workflowTransitionRepository->findOneBy(['name' => $transitionName, 'workflow' => WorkflowType::fromName($workflowName)]);

        if (!$entity instanceof WorkflowTransition) {
            var_dump($this->getJsonContent());
            throw new \Exception('No transition found');
        }
    }
}
