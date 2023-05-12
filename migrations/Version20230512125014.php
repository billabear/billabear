<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230512125014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE charge_back_amount_daily_stats ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE charge_back_amount_monthly_stats ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE charge_back_amount_yearly_stats ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE payment_amount_daily_stats ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE payment_amount_monthly_stats ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE payment_amount_yearly_stats ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE refund_amount_daily_stats ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE refund_amount_monthly_stats ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE refund_amount_yearly_stats ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE subscription_cancellation_daily_stats ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE subscription_cancellation_monthly_stats ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE subscription_cancellation_yearly_stats ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE subscription_daily_stats ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE subscription_monthly_stats ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE subscription_yearly_stats ADD date DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE payment_amount_daily_stats DROP date');
        $this->addSql('ALTER TABLE payment_amount_monthly_stats DROP date');
        $this->addSql('ALTER TABLE subscription_daily_stats DROP date');
        $this->addSql('ALTER TABLE subscription_cancellation_monthly_stats DROP date');
        $this->addSql('ALTER TABLE charge_back_amount_daily_stats DROP date');
        $this->addSql('ALTER TABLE subscription_cancellation_yearly_stats DROP date');
        $this->addSql('ALTER TABLE payment_amount_yearly_stats DROP date');
        $this->addSql('ALTER TABLE refund_amount_yearly_stats DROP date');
        $this->addSql('ALTER TABLE charge_back_amount_yearly_stats DROP date');
        $this->addSql('ALTER TABLE refund_amount_daily_stats DROP date');
        $this->addSql('ALTER TABLE subscription_yearly_stats DROP date');
        $this->addSql('ALTER TABLE subscription_cancellation_daily_stats DROP date');
        $this->addSql('ALTER TABLE subscription_monthly_stats DROP date');
        $this->addSql('ALTER TABLE charge_back_amount_monthly_stats DROP date');
        $this->addSql('ALTER TABLE refund_amount_monthly_stats DROP date');
    }
}
