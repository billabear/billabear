<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241215015503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '2024.02.01 migration 2';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE usage_limits (id UUID NOT NULL, customer_id UUID DEFAULT NULL, amount INT NOT NULL, warning_level INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C4C29C39395C3F3 ON usage_limits (customer_id)');
        $this->addSql('COMMENT ON COLUMN usage_limits.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN usage_limits.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE usage_warning (id UUID NOT NULL, customer_id UUID DEFAULT NULL, usage_limit_id UUID DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, start_of_period TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_of_period TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1ECAEAD99395C3F3 ON usage_warning (customer_id)');
        $this->addSql('CREATE INDEX IDX_1ECAEAD92C0BC879 ON usage_warning (usage_limit_id)');
        $this->addSql('COMMENT ON COLUMN usage_warning.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN usage_warning.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN usage_warning.usage_limit_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE usage_limits ADD CONSTRAINT FK_C4C29C39395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE usage_warning ADD CONSTRAINT FK_1ECAEAD99395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE usage_warning ADD CONSTRAINT FK_1ECAEAD92C0BC879 FOREIGN KEY (usage_limit_id) REFERENCES usage_limits (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customers ADD warning_level INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line ALTER net_price DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usage_limits DROP CONSTRAINT FK_C4C29C39395C3F3');
        $this->addSql('ALTER TABLE usage_warning DROP CONSTRAINT FK_1ECAEAD99395C3F3');
        $this->addSql('ALTER TABLE usage_warning DROP CONSTRAINT FK_1ECAEAD92C0BC879');
        $this->addSql('DROP TABLE usage_limits');
        $this->addSql('DROP TABLE usage_warning');
        $this->addSql('ALTER TABLE invoice_line ALTER net_price SET DEFAULT 0');
        $this->addSql('ALTER TABLE customers DROP warning_level');
    }
}
