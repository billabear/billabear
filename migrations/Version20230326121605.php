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
final class Version20230326121605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE price (id UUID NOT NULL, product_id UUID DEFAULT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, recurring BOOLEAN DEFAULT NULL, schedule VARCHAR(255) DEFAULT NULL, external_reference VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CAC822D94584665A ON price (product_id)');
        $this->addSql('COMMENT ON COLUMN price.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN price.product_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE product (id UUID NOT NULL, name VARCHAR(255) NOT NULL, external_reference VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN product.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE subscription_feature (id UUID NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN subscription_feature.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE subscription_limit (id UUID NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN subscription_limit.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE subscription_plan (id UUID NOT NULL, public BOOLEAN NOT NULL, name VARCHAR(255) NOT NULL, external_reference VARCHAR(255) DEFAULT NULL, external_referenceLink VARCHAR(255) DEFAULT NULL, per_seat BOOLEAN DEFAULT NULL, is_free BOOLEAN DEFAULT NULL, user_count INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN subscription_plan.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE subscription_plan_subscription_feature (subscription_plan_id UUID NOT NULL, subscription_feature_id UUID NOT NULL, PRIMARY KEY(subscription_plan_id, subscription_feature_id))');
        $this->addSql('CREATE INDEX IDX_63CBB01D9B8CE200 ON subscription_plan_subscription_feature (subscription_plan_id)');
        $this->addSql('CREATE INDEX IDX_63CBB01DA201F81C ON subscription_plan_subscription_feature (subscription_feature_id)');
        $this->addSql('COMMENT ON COLUMN subscription_plan_subscription_feature.subscription_plan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription_plan_subscription_feature.subscription_feature_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE subscription_plan_price (subscription_plan_id UUID NOT NULL, price_id UUID NOT NULL, PRIMARY KEY(subscription_plan_id, price_id))');
        $this->addSql('CREATE INDEX IDX_5B8B27409B8CE200 ON subscription_plan_price (subscription_plan_id)');
        $this->addSql('CREATE INDEX IDX_5B8B2740D614C7E7 ON subscription_plan_price (price_id)');
        $this->addSql('COMMENT ON COLUMN subscription_plan_price.subscription_plan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription_plan_price.price_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE subscription_plan_limit (id UUID NOT NULL, subscription_limit_id UUID DEFAULT NULL, subscription_plan_id UUID DEFAULT NULL, limit_number INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EED5EDF9C6F31ED6 ON subscription_plan_limit (subscription_limit_id)');
        $this->addSql('CREATE INDEX IDX_EED5EDF99B8CE200 ON subscription_plan_limit (subscription_plan_id)');
        $this->addSql('COMMENT ON COLUMN subscription_plan_limit.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription_plan_limit.subscription_limit_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription_plan_limit.subscription_plan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D94584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan_subscription_feature ADD CONSTRAINT FK_63CBB01D9B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plan (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan_subscription_feature ADD CONSTRAINT FK_63CBB01DA201F81C FOREIGN KEY (subscription_feature_id) REFERENCES subscription_feature (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan_price ADD CONSTRAINT FK_5B8B27409B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plan (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan_price ADD CONSTRAINT FK_5B8B2740D614C7E7 FOREIGN KEY (price_id) REFERENCES price (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan_limit ADD CONSTRAINT FK_EED5EDF9C6F31ED6 FOREIGN KEY (subscription_limit_id) REFERENCES subscription_limit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan_limit ADD CONSTRAINT FK_EED5EDF99B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE price DROP CONSTRAINT FK_CAC822D94584665A');
        $this->addSql('ALTER TABLE subscription_plan_subscription_feature DROP CONSTRAINT FK_63CBB01D9B8CE200');
        $this->addSql('ALTER TABLE subscription_plan_subscription_feature DROP CONSTRAINT FK_63CBB01DA201F81C');
        $this->addSql('ALTER TABLE subscription_plan_price DROP CONSTRAINT FK_5B8B27409B8CE200');
        $this->addSql('ALTER TABLE subscription_plan_price DROP CONSTRAINT FK_5B8B2740D614C7E7');
        $this->addSql('ALTER TABLE subscription_plan_limit DROP CONSTRAINT FK_EED5EDF9C6F31ED6');
        $this->addSql('ALTER TABLE subscription_plan_limit DROP CONSTRAINT FK_EED5EDF99B8CE200');
        $this->addSql('DROP TABLE price');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE subscription_feature');
        $this->addSql('DROP TABLE subscription_limit');
        $this->addSql('DROP TABLE subscription_plan');
        $this->addSql('DROP TABLE subscription_plan_subscription_feature');
        $this->addSql('DROP TABLE subscription_plan_price');
        $this->addSql('DROP TABLE subscription_plan_limit');
    }
}
