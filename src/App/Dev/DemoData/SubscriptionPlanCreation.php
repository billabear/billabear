<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dev\DemoData;

use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Entity\SubscriptionFeature;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Entity\SubscriptionPlanLimit;
use Parthenon\Billing\Obol\PriceRegisterInterface;
use Parthenon\Billing\Obol\ProductRegisterInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\ProductRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionFeatureRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class SubscriptionPlanCreation
{
    public function __construct(
        private SubscriptionFeatureRepositoryInterface $subscriptionFeatureRepository,
        private ProductRepositoryInterface $productRepository,
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private ProductRegisterInterface $productRegister,
        private PriceRepositoryInterface $priceRepository,
        private PriceRegisterInterface $priceRegister,
    ) {
    }

    public function createData(OutputInterface $output): void
    {
        $output->writeln("\nCreate features");
        $faker = \Faker\Factory::create();
        $progressBar = new ProgressBar($output, 24);

        $code = $faker->randomLetter.$faker->randomLetter.$faker->randomLetter;
        $featureNames = ['feature_one_'.$code, 'feature_two_'.$code, 'feature_three_'.$code, 'feature_four_'.$code, 'feature_five_'.$code, 'feature_six_'.$code, 'feature_seven_'.$code, 'feature_eight_'.$code, 'feature_nine_'.$code, 'feature_ten_'.$code, 'feature_eleven_'.$code, 'feature_twelve_'.$code];
        $limitNames = ['limit_one_'.$code, 'limit_two_'.$code, 'limit_three_'.$code, 'limit_four_'.$code, 'limit_five_'.$code, 'limit_six_'.$code, 'limit_seven_'.$code, 'limit_eight_'.$code, 'limit_nine_'.$code, 'limit_ten_'.$code, 'limit_eleven_'.$code, 'limit_twelve_'.$code];
        $features = [];
        $limits = [];
        $progressBar->start();
        foreach ($featureNames as $featureCode) {
            $feature = new SubscriptionFeature();
            $feature->setName($featureCode);
            $feature->setCode($featureCode);
            $feature->setDescription('A test demo feature - '.$featureCode);

            $this->subscriptionFeatureRepository->save($feature);
            $features[] = $feature;
            $progressBar->advance();
        }
        foreach ($limitNames as $featureCode) {
            $feature = new SubscriptionFeature();
            $feature->setName($featureCode);
            $feature->setCode($featureCode);
            $feature->setDescription('A test demo feature - '.$featureCode);

            $this->subscriptionFeatureRepository->save($feature);
            $progressBar->advance();
            $limits[] = $feature;
        }
        $progressBar->finish();

        $progressBar = new ProgressBar($output, 10);

        $output->writeln("\nCreate Products");
        $progressBar->start();
        $products = [];
        $prices = [];
        for ($i = 0; $i < 10; ++$i) {
            $product = new Product();
            $product->setName($faker->domainName);
            $this->productRegister->registerProduct($product);
            $this->productRepository->save($product);
            $products[] = $product;
            $prices[$product->getName()] = [];

            foreach (['USD', 'CAD', 'EUR', 'GBP'] as $currency) {
                $amount = $faker->numberBetween(2000, 4900);
                $yearAmount = $amount * 10;
                $price = new Price();
                $price->setProduct($product);
                $price->setCurrency($currency);
                $price->setAmount($amount);
                $price->setCreatedAt(new \DateTime());
                $price->setIncludingTax(true);
                $price->setPublic(true);
                $price->setSchedule('month');
                $price->setRecurring(true);
                $this->priceRegister->registerPrice($price);
                $this->priceRepository->save($price);
                $prices[$product->getName()][] = $price;

                $price = new Price();
                $price->setProduct($product);
                $price->setCurrency($currency);
                $price->setAmount($yearAmount);
                $price->setCreatedAt(new \DateTime());
                $price->setIncludingTax(true);
                $price->setPublic(true);
                $price->setSchedule('year');
                $price->setRecurring(true);
                $this->priceRegister->registerPrice($price);
                $this->priceRepository->save($price);
                $prices[$product->getName()][] = $price;
            }
            $progressBar->advance();
        }
        $progressBar->finish();

        $output->writeln("\nCreate Subscription Plans");
        $progressBar = new ProgressBar($output, 10);
        $progressBar->start();
        foreach ($products as $product) {
            $subscriptionPlan = new SubscriptionPlan();
            $subscriptionPlan->setName($faker->name);
            $subscriptionPlan->setPublic(true);
            $subscriptionPlan->setProduct($product);
            $subscriptionPlan->setFree(false);
            $subscriptionPlan->setUserCount(10);
            $subscriptionPlan->setPerSeat(false);
            foreach ($prices[$product->getName()] as $price) {
                $subscriptionPlan->addPrice($price);
            }

            foreach ($features as $item) {
                $subscriptionPlan->addFeature($item);
            }

            foreach ($limits as $limit) {
                $subscriptionPlanLimit = new SubscriptionPlanLimit();
                $subscriptionPlanLimit->setSubscriptionFeature($limit);
                $subscriptionPlanLimit->setSubscriptionPlan($subscriptionPlan);
                $subscriptionPlanLimit->setLimit($faker->numberBetween(1, 100));
                $subscriptionPlan->addLimit($subscriptionPlanLimit);
            }
            $this->subscriptionPlanRepository->save($subscriptionPlan);
            $progressBar->advance();
        }

        $progressBar->finish();
    }
}
