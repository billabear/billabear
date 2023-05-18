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
final class Version20230518163554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE credit_note DROP CONSTRAINT fk_c87f4529bb9d6fee');
        $this->addSql('DROP INDEX idx_c87f4529bb9d6fee');
        $this->addSql('ALTER TABLE credit_note ADD completely_used BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE credit_note RENAME COLUMN issuer_id TO billing_admin_id');
        $this->addSql('ALTER TABLE credit_note ADD CONSTRAINT FK_C87F45297CF7EBEC FOREIGN KEY (billing_admin_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C87F45297CF7EBEC ON credit_note (billing_admin_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE credit_note DROP CONSTRAINT FK_C87F45297CF7EBEC');
        $this->addSql('DROP INDEX IDX_C87F45297CF7EBEC');
        $this->addSql('ALTER TABLE credit_note DROP completely_used');
        $this->addSql('ALTER TABLE credit_note RENAME COLUMN billing_admin_id TO issuer_id');
        $this->addSql('ALTER TABLE credit_note ADD CONSTRAINT fk_c87f4529bb9d6fee FOREIGN KEY (issuer_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_c87f4529bb9d6fee ON credit_note (issuer_id)');
    }
}
