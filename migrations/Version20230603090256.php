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
final class Version20230603090256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vouchers (id UUID NOT NULL, billing_admin_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, entry_type VARCHAR(255) NOT NULL, value INT DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, entry_event VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_931507487CF7EBEC ON vouchers (billing_admin_id)');
        $this->addSql('COMMENT ON COLUMN vouchers.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN vouchers.billing_admin_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE vouchers_amount (id UUID NOT NULL, voucher_id UUID DEFAULT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1A86CADF28AA1B6F ON vouchers_amount (voucher_id)');
        $this->addSql('COMMENT ON COLUMN vouchers_amount.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN vouchers_amount.voucher_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE vouchers ADD CONSTRAINT FK_931507487CF7EBEC FOREIGN KEY (billing_admin_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vouchers_amount ADD CONSTRAINT FK_1A86CADF28AA1B6F FOREIGN KEY (voucher_id) REFERENCES vouchers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vouchers DROP CONSTRAINT FK_931507487CF7EBEC');
        $this->addSql('ALTER TABLE vouchers_amount DROP CONSTRAINT FK_1A86CADF28AA1B6F');
        $this->addSql('DROP TABLE vouchers');
        $this->addSql('DROP TABLE vouchers_amount');
    }
}
