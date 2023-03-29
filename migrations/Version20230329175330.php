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
final class Version20230329175330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_plan_limit DROP CONSTRAINT fk_eed5edf9c6f31ed6');
        $this->addSql('DROP TABLE subscription_limit');
        $this->addSql('DROP INDEX idx_eed5edf9c6f31ed6');
        $this->addSql('ALTER TABLE subscription_plan_limit RENAME COLUMN subscription_limit_id TO subscription_feature_id');
        $this->addSql('ALTER TABLE subscription_plan_limit ADD CONSTRAINT FK_EED5EDF9A201F81C FOREIGN KEY (subscription_feature_id) REFERENCES subscription_feature (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_EED5EDF9A201F81C ON subscription_plan_limit (subscription_feature_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE subscription_limit (id UUID NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN subscription_limit.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE subscription_plan_limit DROP CONSTRAINT FK_EED5EDF9A201F81C');
        $this->addSql('DROP INDEX IDX_EED5EDF9A201F81C');
        $this->addSql('ALTER TABLE subscription_plan_limit RENAME COLUMN subscription_feature_id TO subscription_limit_id');
        $this->addSql('ALTER TABLE subscription_plan_limit ADD CONSTRAINT fk_eed5edf9c6f31ed6 FOREIGN KEY (subscription_limit_id) REFERENCES subscription_limit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_eed5edf9c6f31ed6 ON subscription_plan_limit (subscription_limit_id)');
    }
}
