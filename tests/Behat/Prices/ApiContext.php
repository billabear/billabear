<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Prices;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Behat\Step\Given;
use BillaBear\Entity\Price;
use BillaBear\Entity\TierComponent;
use BillaBear\Pricing\Usage\MetricType;
use BillaBear\Repository\Orm\MetricRepository;
use BillaBear\Repository\Orm\PriceRepository;
use BillaBear\Repository\Orm\ProductRepository;
use BillaBear\Tests\Behat\Products\ProductTrait;
use BillaBear\Tests\Behat\SendRequestTrait;
use BillaBear\Tests\Behat\Usage\MetricTrait;
use Parthenon\Billing\Enum\PriceType;

class ApiContext implements Context
{
    use SendRequestTrait;
    use ProductTrait;
    use MetricTrait;

    public function __construct(
        private Session $session,
        private ProductRepository $productRepository,
        private PriceRepository $priceRepository,
        private MetricRepository $metricRepository,
    ) {
    }

    /**
     * @When I create a price for the product :arg1
     */
    public function iCreateAPriceForTheProduct($name, TableNode $table)
    {
        $product = $this->getProductByName($name);

        $data = $table->getRowsHash();

        $payload = [
            'amount' => (int) $data['Amount'],
            'currency' => $data['Currency'],
            'recurring' => (true === strtolower($data['Recurring'])),
        ];

        $this->sendJsonRequest('POST', '/api/v1/product/'.$product->getId().'/price', $payload);
    }

    /**
     * @Then there should be a price for :arg1 with the amount :arg2
     */
    public function thereShouldBeAPriceForWithTheAmount($productName, $amount)
    {
        $product = $this->getProductByName($productName);

        /** @var Price[] $prices */
        $prices = $this->priceRepository->findBy(['product' => $product, 'isDeleted' => false]);

        if (empty($prices)) {
            var_dump($this->getJsonContent());
            throw new \Exception('Price not found');
        }

        foreach ($prices as $price) {
            if ($price->getAmount() == $amount) {
                return;
            }
        }

        throw new \Exception("Can't find price");
    }

    /**
     * @Then there should be a package price for :arg1 with the amount :arg2 and :arg3 units
     */
    public function thereShouldBeAPackagePriceForWithTheAmountAndUnits($productName, $amount, $units)
    {
        $product = $this->getProductByName($productName);

        /** @var Price[] $prices */
        $prices = $this->priceRepository->findBy(['product' => $product, 'isDeleted' => false, 'type' => PriceType::PACKAGE]);

        if (empty($prices)) {
            throw new \Exception('Price not found');
        }

        foreach ($prices as $price) {
            if ($price->getAmount() == $amount && $price->getUnits() == $units) {
                return;
            }
        }

        throw new \Exception("Can't find price");
    }

    /**
     * @Then there should not be a usage package price for :arg1 with the amount :arg2 and :arg3 units
     */
    public function thereShouldNotBeAUsagePackagePriceForWithTheAmountAndUnits($productName, $amount, $units)
    {
        try {
            $this->thereShouldBeAUsagePackagePriceForWithTheAmountAndUnits($productName, $amount, $units);
            throw new \Exception('Found price');
        } catch (\Exception) {
        }
    }

    /**
     * @Then there should be a usage package price for :arg1 with the amount :arg2 and :arg3 units
     */
    public function thereShouldBeAUsagePackagePriceForWithTheAmountAndUnits($productName, $amount, $units)
    {
        $product = $this->getProductByName($productName);

        /** @var Price[] $prices */
        $prices = $this->priceRepository->findBy(['product' => $product, 'isDeleted' => false, 'usage' => true, 'type' => PriceType::PACKAGE]);

        if (empty($prices)) {
            throw new \Exception('Price not found');
        }

        foreach ($prices as $price) {
            if ($price->getAmount() == $amount && $price->getUnits() == $units) {
                return;
            }
        }

        throw new \Exception("Can't find price");
    }

    /**
     * @Then there should be a tier volume price for :arg1 with the following tiers:
     */
    public function thereShouldBeATierVolumePriceForWithTheFollowingTiers($productName, TableNode $table)
    {
        $rawTiers = $table->getColumnsHash();
        $found = 0;
        $product = $this->getProductByName($productName);

        /** @var Price[] $prices */
        $prices = $this->priceRepository->findBy(['product' => $product, 'isDeleted' => false, 'type' => PriceType::TIERED_VOLUME]);

        foreach ($prices as $price) {
            /** @var TierComponent[] $tiers */
            $tiers = $price->getTierComponents();
            foreach ($tiers as $tier) {
                foreach ($rawTiers as $rawTier) {
                    if (
                        intval($rawTier['First Unit']) === $tier->getFirstUnit()
                        && intval($rawTier['Last Unit']) === $tier->getLastUnit()
                        && intval($rawTier['Unit Price']) === $tier->getUnitPrice()
                        && intval($rawTier['Flat Fee']) === $tier->getFlatFee()
                    ) {
                        ++$found;
                        break;
                    }
                }
            }
        }

        if ($found !== count($rawTiers)) {
            var_dump($this->getJsonContent());
            throw new \Exception(sprintf('Found %d but expected to find %d', $found, count($rawTiers)));
        }
    }

    /**
     * @Then there should not be a tier volume price for :arg1
     */
    public function thereShouldNotBeATierVolumePriceFor($productName)
    {
        $product = $this->getProductByName($productName);
        $prices = $this->priceRepository->findBy(['product' => $product, 'isDeleted' => false, 'type' => PriceType::TIERED_VOLUME]);

        if (!empty($prices)) {
            throw new \Exception('Found prices');
        }
    }

    /**
     * @Then there should not be a price for :arg1 with the amount :arg2
     */
    public function thereShouldNotBeAPriceForWithTheAmount($productName, $amount)
    {
        $product = $this->getProductByName($productName);

        /** @var Price[] $prices */
        $prices = $this->priceRepository->findBy(['product' => $product, 'isDeleted' => false]);

        foreach ($prices as $price) {
            if ($price->getAmount() == $amount) {
                throw new \Exception('Found price');
            }
        }
    }

    /**
     * @Given the follow price :arg1 for :arg2 monthly price with a usage tiered graduated for metric :arg3 with these tiers:
     */
    public function theFollowPriceForMonthlyPriceWithAUsageTieredGraduatedForMetricWithTheseTiers($productName, $currency, $metricName, TableNode $table)
    {
        $this->createMetricPrice($productName, $metricName, $currency, 'month', $table);
    }

    #[Given('the a price for :productName for :amount :currency monthly price for package of :unitCount units for metric :metricName')]
    public function theAPriceForForMonthlyPriceForPackageOfUnitsForMetric(string $productName, string $currency, int $amount, int $units, string $metricName): void
    {
        $product = $this->getProductByName($productName);
        $metric = $this->getMetric($metricName);
        $price = new Price();
        $price->setProduct($product);
        $price->setMetric($metric);
        $price->setAmount($amount);
        $price->setCurrency($currency);
        $price->setCreatedAt(new \DateTime('now'));
        $price->setType(PriceType::PACKAGE);
        $price->setMetricType(MetricType::RESETTABLE);
        $price->setRecurring(true);
        $price->setUsage(true);
        $price->setSchedule('month');
        $price->setUnits($units);

        $this->priceRepository->getEntityManager()->persist($price);
        $this->priceRepository->getEntityManager()->flush();
    }

    /**
     * @Given the follow price :arg1 for :arg2 monthly price with a usage tiered graduated for a continuous metric :arg3 with these tiers:
     */
    public function theFollowPriceForMonthlyPriceWithAUsageTieredGraduatedForAContinuousMetricWithTheseTiers($productName, $currency, $metricName, TableNode $table)
    {
        $this->createMetricPrice($productName, $metricName, $currency, 'month', $table, MetricType::CONTINUOUS);
    }

    /**
     * @Given the follow price :arg1 for :arg2 weekly price with a usage tiered graduated for metric :arg3 with these tiers:
     */
    public function theFollowPriceForWeeklyPriceWithAUsageTieredGraduatedForMetricWithTheseTiers($productName, $currency, $metricName, TableNode $table)
    {
        $time = 'week';
        $this->createMetricPrice($productName, $metricName, $currency, $time, $table);
    }

    /**
     * @Given the follow price :arg1 for :arg2 yearly price with a usage tiered graduated for metric :arg3 with these tiers:
     */
    public function theFollowPriceForYearlyPriceWithAUsageTieredGraduatedForMetricWithTheseTiers($productName, $currency, $metricName, TableNode $table)
    {
        $this->createMetricPrice($productName, $metricName, $currency, 'year', $table);
    }

    /**
     * @Given the follow price :arg1 for :arg2 yearly price with a continuous usage tiered graduated for metric :arg3 with these tiers:
     */
    public function theFollowPriceForYearlyPriceWithAContinuousUsageTieredGraduatedForMetricWithTheseTiers($productName, $currency, $metricName, TableNode $table)
    {
        $this->createMetricPrice($productName, $metricName, $currency, 'year', $table, MetricType::CONTINUOUS);
    }

    /**
     * @Given the follow price :arg1 for :arg2 monthly price with tiered graduated with these tiers:
     */
    public function theFollowPriceForMonthlyPriceWithTieredGraduatedWithTheseTiers($productName, $currency, TableNode $table)
    {
        $product = $this->getProductByName($productName);
        $price = new Price();
        $price->setProduct($product);
        $price->setCurrency($currency);
        $price->setCreatedAt(new \DateTime('now'));
        $price->setType(PriceType::TIERED_GRADUATED);
        $price->setRecurring(true);
        $price->setSchedule('month');

        $components = [];
        foreach ($table->getColumnsHash() as $row) {
            $tierComponent = new TierComponent();
            $tierComponent->setPrice($price);
            $tierComponent->setFirstUnit(intval($row['First Unit']));
            if (isset($row['Last Unit']) && '-1' !== $row['Last Unit']) {
                $tierComponent->setLastUnit(intval($row['Last Unit']));
            }
            $tierComponent->setUnitPrice(intval($row['Unit Price']));
            $tierComponent->setFlatFee(intval($row['Flat Fee']));
            $components[] = $tierComponent;
        }
        $price->setTierComponents($components);
        $this->priceRepository->getEntityManager()->persist($price);
        $this->priceRepository->getEntityManager()->flush();
    }

    /**
     * @Given the follow price :arg1 for :arg2 monthly price with tiered volume with these tiers:
     */
    public function theFollowPriceForMonthlyPriceWithTieredVolumeWithTheseTiers($productName, $currency, TableNode $table)
    {
        $product = $this->getProductByName($productName);
        $price = new Price();
        $price->setProduct($product);
        $price->setCurrency($currency);
        $price->setCreatedAt(new \DateTime('now'));
        $price->setType(PriceType::TIERED_VOLUME);
        $price->setRecurring(true);
        $price->setSchedule('month');

        $components = [];
        foreach ($table->getColumnsHash() as $row) {
            $tierComponent = new TierComponent();
            $tierComponent->setPrice($price);
            $tierComponent->setFirstUnit(intval($row['First Unit']));
            if (isset($row['Last Unit']) && '-1' !== $row['Last Unit']) {
                $tierComponent->setLastUnit(intval($row['Last Unit']));
            }
            $tierComponent->setUnitPrice(intval($row['Unit Price']));
            $tierComponent->setFlatFee(intval($row['Flat Fee']));
            $components[] = $tierComponent;
        }
        $price->setTierComponents($components);
        $this->priceRepository->getEntityManager()->persist($price);
        $this->priceRepository->getEntityManager()->flush();
    }

    /**
     * @Given the follow prices exist:
     */
    public function theFollowPricesExist(TableNode $table)
    {
        $data = $table->getColumnsHash();

        foreach ($data as $row) {
            $product = $this->getProductByName($row['Product']);

            $price = new Price();
            $price->setProduct($product);
            $price->setAmount($row['Amount']);
            $price->setExternalReference(bin2hex(random_bytes(12)));
            $price->setCurrency($row['Currency']);
            $price->setRecurring('true' === strtolower($row['Recurring']));
            if (!isset($row['Type'])) {
                $type = $price->isRecurring() ? PriceType::FIXED_PRICE : PriceType::ONE_OFF;
            } else {
                $type = PriceType::from($row['Type']);
            }

            $price->setType($type);
            if (isset($row['Schedule']) && !empty($row['Schedule'])) {
                $price->setSchedule($row['Schedule']);
            }
            if (isset($row['Units'])) {
                $price->setUnits(intval($row['Units']));
            }
            $price->setPublic('true' === strtolower($row['Public'] ?? 'true'));
            $price->setCreatedAt(new \DateTime('now'));
            $price->setIncludingTax('true' === strtolower($row['Include Tax'] ?? 'true'));
            $this->priceRepository->getEntityManager()->persist($price);
        }
        $this->priceRepository->getEntityManager()->flush();
    }

    /**
     * @When I fetch all prices for the product :arg1 via API
     */
    public function iFetchAllPricesForTheProductViaApi($productName)
    {
        $product = $this->getProductByName($productName);
        $this->sendJsonRequest('GET', '/api/v1/product/'.$product->getId().'/price');
    }

    /**
     * @Then there should be a price for :arg1 in the data set
     */
    public function thereShouldBeAPriceForInTheDataSet($amount)
    {
        $json = $this->getJsonContent();

        foreach ($json['data'] as $price) {
            if ($amount == $price['amount']) {
                return;
            }
        }

        throw new \Exception("Can't find price");
    }

    /**
     * @throws \Exception
     */
    public function createMetricPrice($productName, $metricName, $currency, string $time, TableNode $table, MetricType $metricType = MetricType::RESETTABLE): void
    {
        $product = $this->getProductByName($productName);
        $metric = $this->getMetric($metricName);
        $price = new Price();
        $price->setProduct($product);
        $price->setMetric($metric);
        $price->setCurrency($currency);
        $price->setCreatedAt(new \DateTime('now'));
        $price->setType(PriceType::TIERED_GRADUATED);
        $price->setMetricType($metricType);
        $price->setRecurring(true);
        $price->setUsage(true);
        $price->setSchedule($time);

        $components = [];
        foreach ($table->getColumnsHash() as $row) {
            $tierComponent = new TierComponent();
            $tierComponent->setPrice($price);
            $tierComponent->setFirstUnit(intval($row['First Unit']));
            if (isset($row['Last Unit']) && '-1' !== $row['Last Unit']) {
                $tierComponent->setLastUnit(intval($row['Last Unit']));
            }
            $tierComponent->setUnitPrice(intval($row['Unit Price']));
            $tierComponent->setFlatFee(intval($row['Flat Fee']));
            $components[] = $tierComponent;
        }
        $price->setTierComponents($components);
        $this->priceRepository->getEntityManager()->persist($price);
        $this->priceRepository->getEntityManager()->flush();
    }
}
