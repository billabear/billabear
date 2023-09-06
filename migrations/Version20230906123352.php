<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230906123352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Version 1.1.2';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscription_seat_modification (id UUID NOT NULL, subscription_id UUID DEFAULT NULL, type VARCHAR(255) NOT NULL, change_value INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E532907B9A1887DC ON subscription_seat_modification (subscription_id)');
        $this->addSql('COMMENT ON COLUMN subscription_seat_modification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription_seat_modification.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE subscription_seat_modification ADD CONSTRAINT FK_E532907B9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_process ALTER due_at DROP NOT NULL');
        $this->addSql('ALTER TABLE quote_line ADD seat_number INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscription_seat_modification DROP CONSTRAINT FK_E532907B9A1887DC');
        $this->addSql('DROP TABLE subscription_seat_modification');
        $this->addSql('ALTER TABLE invoice_process ALTER due_at SET NOT NULL');
        $this->addSql('ALTER TABLE quote_line DROP seat_number');
    }
}
