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
final class Version20230608113932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX brand_code_idx ON brand_settings (code)');
        $this->addSql('ALTER INDEX idx_1cc16efe9395c3f3 RENAME TO customer_idx');
        $this->addSql('CREATE INDEX email_idx ON customers (billing_email)');
        $this->addSql('CREATE INDEX external_ref_idx ON customers (external_customer_reference)');
        $this->addSql('DROP INDEX name_locale');
        $this->addSql('CREATE UNIQUE INDEX name_locale_brand ON email_templates (name, locale, brand_id)');
        $this->addSql('CREATE INDEX name_locale ON email_templates (name, locale)');
        $this->addSql('CREATE INDEX ex_rates_currency_code_idx ON exchange_rates (currency_code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX ex_rates_currency_code_idx');
        $this->addSql('ALTER INDEX customer_idx RENAME TO idx_1cc16efe9395c3f3');
        $this->addSql('DROP INDEX name_locale_brand');
        $this->addSql('DROP INDEX name_locale');
        $this->addSql('CREATE UNIQUE INDEX name_locale ON email_templates (name, locale)');
        $this->addSql('DROP INDEX email_idx');
        $this->addSql('DROP INDEX external_ref_idx');
        $this->addSql('DROP INDEX brand_code_idx');
    }
}
