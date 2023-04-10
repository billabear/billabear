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
final class Version20230410124216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription ADD payment_details_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription DROP payment_external_reference');
        $this->addSql('ALTER TABLE subscription ALTER has_trial DROP DEFAULT');
        $this->addSql('ALTER TABLE subscription ALTER has_trial SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN subscription.payment_details_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D38EEC86F7 FOREIGN KEY (payment_details_id) REFERENCES payment_details (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A3C664D38EEC86F7 ON subscription (payment_details_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D38EEC86F7');
        $this->addSql('DROP INDEX IDX_A3C664D38EEC86F7');
        $this->addSql('ALTER TABLE subscription ADD payment_external_reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription DROP payment_details_id');
        $this->addSql('ALTER TABLE subscription ALTER has_trial SET DEFAULT false');
        $this->addSql('ALTER TABLE subscription ALTER has_trial DROP NOT NULL');
    }
}
