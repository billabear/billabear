<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023.
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
final class Version20230608120606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX IDX_66414E71BB8273378EB61006E5A02990 ON stats_charge_back_amount_daily (year, month, day)');
        $this->addSql('CREATE INDEX IDX_93F3F329BB8273378EB61006E5A02990 ON stats_charge_back_amount_monthly (year, month, day)');
        $this->addSql('CREATE INDEX IDX_72CED1F5BB8273378EB61006E5A02990 ON stats_charge_back_amount_yearly (year, month, day)');
        $this->addSql('CREATE INDEX IDX_A291DA1ABB8273378EB61006E5A02990 ON stats_payment_amount_daily (year, month, day)');
        $this->addSql('CREATE INDEX IDX_E298D2C8BB8273378EB61006E5A02990 ON stats_payment_amount_monthly (year, month, day)');
        $this->addSql('CREATE INDEX IDX_A86AB9B1BB8273378EB61006E5A02990 ON stats_payment_amount_yearly (year, month, day)');
        $this->addSql('CREATE INDEX IDX_20D50675BB8273378EB61006E5A02990 ON stats_refund_amount_daily (year, month, day)');
        $this->addSql('CREATE INDEX IDX_8F98B91BBB8273378EB61006E5A02990 ON stats_refund_amount_monthly (year, month, day)');
        $this->addSql('CREATE INDEX IDX_75E581A4BB8273378EB61006E5A02990 ON stats_refund_amount_yearly (year, month, day)');
        $this->addSql('CREATE INDEX IDX_5272A11FBB8273378EB61006E5A02990 ON stats_subscription_cancellation_daily (year, month, day)');
        $this->addSql('CREATE INDEX IDX_58387EDABB8273378EB61006E5A02990 ON stats_subscription_cancellation_monthly (year, month, day)');
        $this->addSql('CREATE INDEX IDX_D8F0AE45BB8273378EB61006E5A02990 ON stats_subscription_cancellation_yearly (year, month, day)');
        $this->addSql('CREATE INDEX IDX_5B72B37DBB8273378EB61006E5A02990 ON stats_subscription_daily (year, month, day)');
        $this->addSql('CREATE INDEX IDX_FC4A0FB7BB8273378EB61006E5A02990 ON stats_subscription_monthly (year, month, day)');
        $this->addSql('CREATE INDEX IDX_7B45AE23BB8273378EB61006E5A02990 ON stats_subscription_yearly (year, month, day)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_93F3F329BB8273378EB61006E5A02990');
        $this->addSql('DROP INDEX IDX_FC4A0FB7BB8273378EB61006E5A02990');
        $this->addSql('DROP INDEX IDX_A86AB9B1BB8273378EB61006E5A02990');
        $this->addSql('DROP INDEX IDX_5272A11FBB8273378EB61006E5A02990');
        $this->addSql('DROP INDEX IDX_72CED1F5BB8273378EB61006E5A02990');
        $this->addSql('DROP INDEX IDX_20D50675BB8273378EB61006E5A02990');
        $this->addSql('DROP INDEX IDX_8F98B91BBB8273378EB61006E5A02990');
        $this->addSql('DROP INDEX IDX_7B45AE23BB8273378EB61006E5A02990');
        $this->addSql('DROP INDEX IDX_66414E71BB8273378EB61006E5A02990');
        $this->addSql('DROP INDEX IDX_D8F0AE45BB8273378EB61006E5A02990');
        $this->addSql('DROP INDEX IDX_58387EDABB8273378EB61006E5A02990');
        $this->addSql('DROP INDEX IDX_5B72B37DBB8273378EB61006E5A02990');
        $this->addSql('DROP INDEX IDX_A291DA1ABB8273378EB61006E5A02990');
        $this->addSql('DROP INDEX IDX_E298D2C8BB8273378EB61006E5A02990');
        $this->addSql('DROP INDEX IDX_75E581A4BB8273378EB61006E5A02990');
    }
}
