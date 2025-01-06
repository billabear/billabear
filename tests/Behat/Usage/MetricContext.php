<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Usage;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use BillaBear\Background\Usage\CounterUpdate;
use BillaBear\Entity\Usage\Metric;
use BillaBear\Entity\Usage\MetricCounter;
use BillaBear\Entity\Usage\MetricFilter;
use BillaBear\Pricing\Usage\MetricAggregationMethod;
use BillaBear\Pricing\Usage\MetricEventIngestion;
use BillaBear\Pricing\Usage\MetricFilterType;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Repository\Orm\MetricCounterRepository;
use BillaBear\Repository\Orm\MetricRepository;
use BillaBear\Tests\Behat\Customers\CustomerTrait;
use BillaBear\Tests\Behat\SendRequestTrait;

class MetricContext implements Context
{
    use SendRequestTrait;
    use MetricTrait;
    use MetricUsageTrait;
    use CustomerTrait;

    private array $filters = [];

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
        private MetricRepository $metricRepository,
        private MetricCounterRepository $metricUsageRepository,
        private CounterUpdate $counterUpdate,
    ) {
    }

    /**
     * @BeforeScenario
     */
    public function startUp(BeforeScenarioScope $event)
    {
        $this->filters = [];
    }

    /**
     * @When I create a metric via the app with the following:
     */
    public function iCreateAMetricViaTheAppWithTheFollowing(TableNode $table)
    {
        $data = $table->getRowsHash();
        $payload = [
            'name' => $data['Name'],
            'code' => $data['Code'],
            'aggregation_method' => str_replace(' ', '_', strtolower($data['Aggregation Method'])),
            'aggregation_property' => $data['Aggregation Property'] ?? null,
            'event_ingestion' => str_replace(' ', '_', strtolower($data['Ingestion'])),
            'filters' => $this->filters,
        ];

        $this->sendJsonRequest('POST', '/app/metric', $payload);
    }

    /**
     * @When I update the metric :arg1 via the app with the following:
     */
    public function iUpdateTheMetricViaTheAppWithTheFollowing($metricName, TableNode $table)
    {
        $metric = $this->getMetric($metricName);
        $data = $table->getRowsHash();
        $payload = [
            'name' => $data['Name'],
            'aggregation_method' => str_replace(' ', '_', strtolower($data['Aggregation Method'])),
            'aggregation_property' => $data['Aggregation Property'] ?? null,
            'event_ingestion' => str_replace(' ', '_', strtolower($data['Ingestion'])),
            'filters' => $this->filters,
        ];

        $this->sendJsonRequest('POST', '/app/metric/'.$metric->getId().'/update', $payload);
    }

    /**
     * @Then there should be a metric called :arg1
     */
    public function thereShouldBeAMetricCalled($name)
    {
        $this->getMetric($name);
    }

    /**
     * @Then there should not be a metric called :arg1
     */
    public function thereShouldNotBeAMetricCalled($productName)
    {
        try {
            $this->getMetric($productName);
        } catch (\Exception) {
            return;
        }
        var_dump($this->getJsonContent());
        throw new \Exception('Found metric');
    }

    /**
     * @Given I want to have the following filters on a metric
     */
    public function iWantToHaveTheFollowingFiltersOnAMetric(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $this->filters[] = [
                'name' => $row['Name'],
                'value' => $row['Value'],
                'type' => $row['Type'],
            ];
        }
    }

    /**
     * @Then the metric :arg1 will have a filter for :arg2
     */
    public function theMetricWillHaveAFilterFor($metricName, $filterName)
    {
        $metric = $this->getMetric($metricName);

        foreach ($metric->getFilters() as $filter) {
            if ($filter->getName() === $filterName) {
                return;
            }
        }

        throw new \Exception("Can't find filter for '$filterName'");
    }

    /**
     * @Given the follow metrics exist:
     */
    public function theFollowMetricsExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $metric = new Metric();
            $metric->setName($row['Name']);
            $metric->setCode($row['Code']);
            $metric->setEventIngestion(MetricEventIngestion::from(str_replace(' ', '_', strtolower($row['Ingestion']))));
            $metric->setAggregationMethod(MetricAggregationMethod::from(str_replace(' ', '_', strtolower($row['Aggregation Method']))));
            $metric->setAggregationProperty($row['Aggregation Property']);
            $metric->setCreatedAt(new \DateTime($row['Created At'] ?? 'now'));

            if (isset($row['Filters'])) {
                $output = [];
                $filters = json_decode($row['Filters'], true);
                foreach ($filters as $key => $data) {
                    $filter = new MetricFilter();
                    $filter->setMetric($metric);
                    $filter->setName($key);
                    $filter->setValue($data['value']);
                    $type = MetricFilterType::from($data['type']);
                    $filter->setType($type);
                    $output[] = $filter;
                }
                $metric->setFilters($output);
            }

            $this->metricRepository->getEntityManager()->persist($metric);
        }
        $this->metricRepository->getEntityManager()->flush();
    }

    /**
     * @When I go to the metric list page in the app
     */
    public function iGoToTheMetricListPageInTheApp()
    {
        $this->sendJsonRequest('GET', '/app/metric/list');
    }

    /**
     * @Then I will see :arg1 items in the list
     */
    public function iWillSeeItemsInTheList($count)
    {
        $data = $this->getJsonContent();

        if (count($data['data']) != intval($count)) {
            throw new \Exception(sprintf('Expected %d but got %d items', $count, count($data['data'])));
        }
    }

    /**
     * @Then I will see a metric with the name :arg1
     */
    public function iWillSeeAMetricWithTheName($metricName)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $metric) {
            if ($metric['name'] === $metricName) {
                return;
            }
        }

        throw new \Exception(sprintf("Did not find a metric with the name '%s'", $metricName));
    }

    /**
     * @Given the metric usage for :arg1 and metric :arg2 that has the value :arg3
     */
    public function theMetricMonitorForAndMetricThatHasTheValue($customerEmail, $metricName, $value)
    {
        $usage = $this->getMetricUsage($customerEmail, $metricName);
        $usage->setValue(floatval($value));
        $usage->setUpdatedAt(new \DateTime());

        $this->metricUsageRepository->getEntityManager()->persist($usage);
        $this->metricUsageRepository->getEntityManager()->flush();
    }

    /**
     * @Then the metric usage for :arg1 and metric :arg2 will have the value :arg3
     */
    public function theMetricMonitorForAndMetricWillHaveTheValue($customerEmail, $metricName, $value)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $metric = $this->getMetric($metricName);
        $usage = $this->metricUsageRepository->findOneBy(['customer' => $customer, 'metric' => $metric]);
        if (!$usage instanceof MetricCounter) {
            throw new \Exception("Can't find a metric usage for customer '$customerEmail'");
        }

        if ($usage->getValue() !== floatval($value)) {
            throw new \Exception(sprintf('Expected %f but got %f', $value, $usage->getValue()));
        }
    }

    /**
     * @When the background task to update metric counters is ran
     */
    public function theBackgroundTaskToUpdateMetricCountersIsRan()
    {
        $this->counterUpdate->execute();
    }
}
