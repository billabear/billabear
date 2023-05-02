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
final class Version20230502200142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment_failure_process (id UUID NOT NULL, payment_id UUID DEFAULT NULL, customer_id UUID DEFAULT NULL, state VARCHAR(255) NOT NULL, retry_count INT NOT NULL, resolved BOOLEAN NOT NULL, next_attempt_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F939A8484C3A3BB ON payment_failure_process (payment_id)');
        $this->addSql('CREATE INDEX IDX_F939A8489395C3F3 ON payment_failure_process (customer_id)');
        $this->addSql('COMMENT ON COLUMN payment_failure_process.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment_failure_process.payment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment_failure_process.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment_failure_process ADD CONSTRAINT FK_F939A8484C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_failure_process ADD CONSTRAINT FK_F939A8489395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE payment_failure_process DROP CONSTRAINT FK_F939A8484C3A3BB');
        $this->addSql('ALTER TABLE payment_failure_process DROP CONSTRAINT FK_F939A8489395C3F3');
        $this->addSql('DROP TABLE payment_failure_process');
    }
}
