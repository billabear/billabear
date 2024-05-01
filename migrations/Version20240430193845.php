<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240430193845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // ////////////////
        // / Tax Type Change
        // ////////////////
        $this->addSql('ALTER TABLE checkout_line ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE checkout_line DROP tax_type');
        $this->addSql('COMMENT ON COLUMN checkout_line.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE checkout_line ADD CONSTRAINT FK_3A4D412884042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3A4D412884042C99 ON checkout_line (tax_type_id)');
        $this->addSql('ALTER TABLE checkout_session_line ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE checkout_session_line DROP tax_type');
        $this->addSql('COMMENT ON COLUMN checkout_session_line.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE checkout_session_line ADD CONSTRAINT FK_46316A7D84042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_46316A7D84042C99 ON checkout_session_line (tax_type_id)');
        $this->addSql('ALTER TABLE customers DROP tax_rate_digital');
        $this->addSql('ALTER TABLE invoice_line ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line DROP tax_type');
        $this->addSql('COMMENT ON COLUMN invoice_line.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE invoice_line ADD CONSTRAINT FK_D3D1D69384042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D3D1D69384042C99 ON invoice_line (tax_type_id)');
        $this->addSql('ALTER TABLE payment ADD country VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD physical BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE quote_line ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE quote_line DROP tax_type');
        $this->addSql('COMMENT ON COLUMN quote_line.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE quote_line ADD CONSTRAINT FK_43F3EB7C84042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_43F3EB7C84042C99 ON quote_line (tax_type_id)');
        $this->addSql('ALTER TABLE tax_type ADD is_default BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE tax_type DROP physical');

        // /////////////////
        // / Exchange Rate Modification
        // /////////////////
        $this->addSql('ALTER TABLE exchange_rates ADD original_currency VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE exchange_rates SET original_currency=(SELECT system_settings_main_currency FROM settings WHERE tag = \'default\')');
        $this->addSql('DROP INDEX uniq_5ae3e774fda273ec');
        $this->addSql('DROP INDEX ex_rates_currency_code_idx');
        $this->addSql('ALTER TABLE exchange_rates ALTER original_currency SET NOT NULL');
        $this->addSql('CREATE INDEX ex_rates_currency_code_idx ON exchange_rates (currency_code, original_currency)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exchange_rates DROP original_currency');
        $this->addSql('ALTER TABLE checkout_session_line DROP CONSTRAINT FK_46316A7D84042C99');
        $this->addSql('DROP INDEX IDX_46316A7D84042C99');
        $this->addSql('ALTER TABLE checkout_session_line ADD tax_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE checkout_session_line DROP tax_type_id');
        $this->addSql('ALTER TABLE customers ADD tax_rate_digital DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE checkout_line DROP CONSTRAINT FK_3A4D412884042C99');
        $this->addSql('DROP INDEX IDX_3A4D412884042C99');
        $this->addSql('ALTER TABLE checkout_line ADD tax_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE checkout_line DROP tax_type_id');
        $this->addSql('ALTER TABLE product DROP physical');
        $this->addSql('ALTER TABLE invoice_line DROP CONSTRAINT FK_D3D1D69384042C99');
        $this->addSql('DROP INDEX IDX_D3D1D69384042C99');
        $this->addSql('ALTER TABLE invoice_line ADD tax_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line DROP tax_type_id');
        $this->addSql('ALTER TABLE tax_type ADD physical VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tax_type DROP is_default');
        $this->addSql('ALTER TABLE payment DROP country');
        $this->addSql('ALTER TABLE quote_line DROP CONSTRAINT FK_43F3EB7C84042C99');
        $this->addSql('DROP INDEX IDX_43F3EB7C84042C99');
        $this->addSql('ALTER TABLE quote_line ADD tax_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE quote_line DROP tax_type_id');
    }
}
