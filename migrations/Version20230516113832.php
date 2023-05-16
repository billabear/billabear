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
final class Version20230516113832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice (id UUID NOT NULL, customer_id UUID DEFAULT NULL, invoice_number VARCHAR(255) NOT NULL, valid BOOLEAN NOT NULL, comment VARCHAR(255) DEFAULT NULL, currency VARCHAR(255) NOT NULL, total INT NOT NULL, sub_total INT NOT NULL, vat_total INT NOT NULL, vat_percentage DOUBLE PRECISION NOT NULL, paid BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, biller_address_company_name VARCHAR(255) DEFAULT NULL, biller_address_street_line_one VARCHAR(255) DEFAULT NULL, biller_address_street_line_two VARCHAR(255) DEFAULT NULL, biller_address_city VARCHAR(255) DEFAULT NULL, biller_address_region VARCHAR(255) DEFAULT NULL, biller_address_country VARCHAR(255) DEFAULT NULL, biller_address_postcode VARCHAR(255) DEFAULT NULL, payee_address_company_name VARCHAR(255) DEFAULT NULL, payee_address_street_line_one VARCHAR(255) DEFAULT NULL, payee_address_street_line_two VARCHAR(255) DEFAULT NULL, payee_address_city VARCHAR(255) DEFAULT NULL, payee_address_region VARCHAR(255) DEFAULT NULL, payee_address_country VARCHAR(255) DEFAULT NULL, payee_address_postcode VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_906517442DA68207 ON invoice (invoice_number)');
        $this->addSql('CREATE INDEX IDX_906517449395C3F3 ON invoice (customer_id)');
        $this->addSql('COMMENT ON COLUMN invoice.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE invoice_subscription (invoice_id UUID NOT NULL, subscription_id UUID NOT NULL, PRIMARY KEY(invoice_id, subscription_id))');
        $this->addSql('CREATE INDEX IDX_1C014BA72989F1FD ON invoice_subscription (invoice_id)');
        $this->addSql('CREATE INDEX IDX_1C014BA79A1887DC ON invoice_subscription (subscription_id)');
        $this->addSql('COMMENT ON COLUMN invoice_subscription.invoice_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice_subscription.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE invoice_line (id UUID NOT NULL, invoice_id UUID DEFAULT NULL, currency VARCHAR(255) NOT NULL, total INT NOT NULL, sub_total INT NOT NULL, vat_total INT NOT NULL, vat_percentage BOOLEAN NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D3D1D6932989F1FD ON invoice_line (invoice_id)');
        $this->addSql('COMMENT ON COLUMN invoice_line.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice_line.invoice_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517449395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_subscription ADD CONSTRAINT FK_1C014BA72989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_subscription ADD CONSTRAINT FK_1C014BA79A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_line ADD CONSTRAINT FK_D3D1D6932989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customers ALTER billing_type DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_906517449395C3F3');
        $this->addSql('ALTER TABLE invoice_subscription DROP CONSTRAINT FK_1C014BA72989F1FD');
        $this->addSql('ALTER TABLE invoice_subscription DROP CONSTRAINT FK_1C014BA79A1887DC');
        $this->addSql('ALTER TABLE invoice_line DROP CONSTRAINT FK_D3D1D6932989F1FD');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE invoice_subscription');
        $this->addSql('DROP TABLE invoice_line');
        $this->addSql('ALTER TABLE customers ALTER billing_type SET DEFAULT \'card\'');
    }
}
