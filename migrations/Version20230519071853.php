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
final class Version20230519071853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE credit (id UUID NOT NULL, customer_id UUID DEFAULT NULL, billing_admin_id UUID DEFAULT NULL, creation_type VARCHAR(255) NOT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, used_amount INT NOT NULL, completely_used BOOLEAN NOT NULL, reason VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, valid_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1CC16EFE9395C3F3 ON credit (customer_id)');
        $this->addSql('CREATE INDEX IDX_1CC16EFE7CF7EBEC ON credit (billing_admin_id)');
        $this->addSql('COMMENT ON COLUMN credit.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN credit.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN credit.billing_admin_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFE7CF7EBEC FOREIGN KEY (billing_admin_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE credit_note DROP CONSTRAINT fk_c87f45299395c3f3');
        $this->addSql('ALTER TABLE credit_note DROP CONSTRAINT fk_c87f45297cf7ebec');
        $this->addSql('DROP TABLE credit_note');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE credit_note (id UUID NOT NULL, customer_id UUID DEFAULT NULL, billing_admin_id UUID DEFAULT NULL, creation_type VARCHAR(255) NOT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, used_amount INT NOT NULL, reason VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, valid_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, completely_used BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_c87f45297cf7ebec ON credit_note (billing_admin_id)');
        $this->addSql('CREATE INDEX idx_c87f45299395c3f3 ON credit_note (customer_id)');
        $this->addSql('COMMENT ON COLUMN credit_note.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN credit_note.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN credit_note.billing_admin_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE credit_note ADD CONSTRAINT fk_c87f45299395c3f3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE credit_note ADD CONSTRAINT fk_c87f45297cf7ebec FOREIGN KEY (billing_admin_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE credit DROP CONSTRAINT FK_1CC16EFE9395C3F3');
        $this->addSql('ALTER TABLE credit DROP CONSTRAINT FK_1CC16EFE7CF7EBEC');
        $this->addSql('DROP TABLE credit');
    }
}
