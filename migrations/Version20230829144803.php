<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE.md file.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230829144803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Version 1.1 migrations';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quote ADD expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE quote_line ALTER reverse_charge DROP DEFAULT');
        $this->addSql('ALTER TABLE invoice ADD due_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD system_settings_default_invoice_due_time VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE TABLE invoice_process (id UUID NOT NULL, customer_id UUID DEFAULT NULL, invoice_id UUID DEFAULT NULL, state VARCHAR(255) NOT NULL,due_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULl, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, error VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_74C42E459395C3F3 ON invoice_process (customer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_74C42E452989F1FD ON invoice_process (invoice_id)');
        $this->addSql('COMMENT ON COLUMN invoice_process.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice_process.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice_process.invoice_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE invoice_process ADD CONSTRAINT FK_74C42E459395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_process ADD CONSTRAINT FK_74C42E452989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE brand_settings ADD notification_settings_invoice_overdue BOOLEAN DEFAULT NULL');

        $this->addSql("INSERT INTO email_templates
(id, brand_id, \"name\", locale, subject, use_emsp_template, template_id, template_body)
SELECT 'ee55d93a-7428-43c4-bd1d-0c2455acb9f8'::uuid, b.id, 'invoice_overdue', 'en', 'Your invoice is overdue', false, NULL, '<html>
    <head>
      <title></title>
    </head>
    <body style=\"background: rgb(254,234,0);
background: radial-gradient(circle, rgba(254,234,0,1) 0%, rgba(246,156,0,1) 100%);; color: black;\">
    
    <div style=\"padding-top: 40px;\">
      <div style=\"margin:auto; background-color: white; max-width: 700px; padding: 50px; border-radius: 15px; margin-top: 40px; \">
        <h1 style=\"text-align:center;\"><img src=\"https://ha-static-data.s3.eu-central-1.amazonaws.com/github-readme-logo.png\" alt=\"{{ brand.name  }}\" /></h1>

            You have not paid your invoice. Please see attached and pay promptly.
      </div>
      </div>
    </body>
  </html>'
  FROM brand_settings b
  WHERE b.code = 'default';
");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quote_line ALTER reverse_charge SET DEFAULT false');
        $this->addSql('ALTER TABLE quote DROP expires_at');
        $this->addSql('ALTER TABLE invoice DROP due_at');
        $this->addSql('ALTER TABLE settings DROP system_settings_default_invoice_due_time');
        $this->addSql('ALTER TABLE invoice_process DROP CONSTRAINT FK_74C42E459395C3F3');
        $this->addSql('ALTER TABLE invoice_process DROP CONSTRAINT FK_74C42E452989F1FD');
        $this->addSql('DROP TABLE invoice_process');
        $this->addSql('ALTER TABLE brand_settings DROP notification_settings_invoice_overdue');
    }
}
