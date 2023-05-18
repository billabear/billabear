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
final class Version20230518143040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE credit_note (id UUID NOT NULL, customer_id UUID DEFAULT NULL, issuer_id UUID DEFAULT NULL, creation_type VARCHAR(255) NOT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, used_amount INT NOT NULL, reason VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, valid_until TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C87F45299395C3F3 ON credit_note (customer_id)');
        $this->addSql('CREATE INDEX IDX_C87F4529BB9D6FEE ON credit_note (issuer_id)');
        $this->addSql('COMMENT ON COLUMN credit_note.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN credit_note.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN credit_note.issuer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE credit_note ADD CONSTRAINT FK_C87F45299395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE credit_note ADD CONSTRAINT FK_C87F4529BB9D6FEE FOREIGN KEY (issuer_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ALTER created_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE invoice ALTER paid_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE invoice ALTER updated_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE settings ALTER system_settings_use_stripe_billing DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE credit_note DROP CONSTRAINT FK_C87F45299395C3F3');
        $this->addSql('ALTER TABLE credit_note DROP CONSTRAINT FK_C87F4529BB9D6FEE');
        $this->addSql('DROP TABLE credit_note');
        $this->addSql('ALTER TABLE settings ALTER system_settings_use_stripe_billing SET DEFAULT false');
        $this->addSql('ALTER TABLE invoice ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE invoice ALTER paid_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE invoice ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
    }
}
