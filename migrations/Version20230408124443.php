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
final class Version20230408124443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cancellation_requests (id UUID NOT NULL, subscription_id UUID DEFAULT NULL, billing_admin_id UUID DEFAULT NULL, state VARCHAR(255) NOT NULL, "when" VARCHAR(255) NOT NULL, specific_date TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, refund_type VARCHAR(255) NOT NULL, comment VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9E258B3E9A1887DC ON cancellation_requests (subscription_id)');
        $this->addSql('CREATE INDEX IDX_9E258B3E7CF7EBEC ON cancellation_requests (billing_admin_id)');
        $this->addSql('COMMENT ON COLUMN cancellation_requests.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cancellation_requests.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cancellation_requests.billing_admin_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE cancellation_requests ADD CONSTRAINT FK_9E258B3E9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cancellation_requests ADD CONSTRAINT FK_9E258B3E7CF7EBEC FOREIGN KEY (billing_admin_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE cancellation_requests DROP CONSTRAINT FK_9E258B3E9A1887DC');
        $this->addSql('ALTER TABLE cancellation_requests DROP CONSTRAINT FK_9E258B3E7CF7EBEC');
        $this->addSql('DROP TABLE cancellation_requests');
    }
}
