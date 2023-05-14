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
final class Version20230514173202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE expiring_card_process (id UUID NOT NULL, customer_id UUID DEFAULT NULL, payment_card_id UUID DEFAULT NULL, state VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, error VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CD6C5DF9395C3F3 ON expiring_card_process (customer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CD6C5DF538594CA ON expiring_card_process (payment_card_id)');
        $this->addSql('COMMENT ON COLUMN expiring_card_process.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN expiring_card_process.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN expiring_card_process.payment_card_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE expiring_card_process ADD CONSTRAINT FK_CD6C5DF9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE expiring_card_process ADD CONSTRAINT FK_CD6C5DF538594CA FOREIGN KEY (payment_card_id) REFERENCES payment_card (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE expiring_card_process DROP CONSTRAINT FK_CD6C5DF9395C3F3');
        $this->addSql('ALTER TABLE expiring_card_process DROP CONSTRAINT FK_CD6C5DF538594CA');
        $this->addSql('DROP TABLE expiring_card_process');
    }
}
