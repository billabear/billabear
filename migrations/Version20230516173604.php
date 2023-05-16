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
final class Version20230516173604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice_payment (invoice_id UUID NOT NULL, payment_id UUID NOT NULL, PRIMARY KEY(invoice_id, payment_id))');
        $this->addSql('CREATE INDEX IDX_9FF1B2DE2989F1FD ON invoice_payment (invoice_id)');
        $this->addSql('CREATE INDEX IDX_9FF1B2DE4C3A3BB ON invoice_payment (payment_id)');
        $this->addSql('COMMENT ON COLUMN invoice_payment.invoice_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice_payment.payment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE invoice_payment ADD CONSTRAINT FK_9FF1B2DE2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_payment ADD CONSTRAINT FK_9FF1B2DE4C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE invoice_payment DROP CONSTRAINT FK_9FF1B2DE2989F1FD');
        $this->addSql('ALTER TABLE invoice_payment DROP CONSTRAINT FK_9FF1B2DE4C3A3BB');
        $this->addSql('DROP TABLE invoice_payment');
    }
}
