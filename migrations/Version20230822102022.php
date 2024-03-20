<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE.md file.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230822102022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration from v1.0 to v1.1';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quote (id UUID NOT NULL, customer_id UUID DEFAULT NULL, created_by_id UUID DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, currency VARCHAR(255) NOT NULL, amount_due INT NOT NULL, total INT NOT NULL, sub_total INT NOT NULL, tax_total INT NOT NULL, paid BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6B71CBF49395C3F3 ON quote (customer_id)');
        $this->addSql('CREATE INDEX IDX_6B71CBF4B03A8386 ON quote (created_by_id)');
        $this->addSql('COMMENT ON COLUMN quote.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quote.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quote.created_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE quote_subscription (quote_id UUID NOT NULL, subscription_id UUID NOT NULL, PRIMARY KEY(quote_id, subscription_id))');
        $this->addSql('CREATE INDEX IDX_14273BD5DB805178 ON quote_subscription (quote_id)');
        $this->addSql('CREATE INDEX IDX_14273BD59A1887DC ON quote_subscription (subscription_id)');
        $this->addSql('COMMENT ON COLUMN quote_subscription.quote_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quote_subscription.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE quote_line (id UUID NOT NULL, quote_id UUID DEFAULT NULL, subscription_plan_id UUID DEFAULT NULL, price_id UUID DEFAULT NULL, currency VARCHAR(255) NOT NULL, total INT NOT NULL, sub_total INT NOT NULL, tax_total INT NOT NULL, tax_percentage DOUBLE PRECISION DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, tax_type VARCHAR(255) DEFAULT NULL, include_tax BOOLEAN NOT NULL, tax_country VARCHAR(255) DEFAULT NULL, reverse_charge BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_43F3EB7CDB805178 ON quote_line (quote_id)');
        $this->addSql('CREATE INDEX IDX_43F3EB7C9B8CE200 ON quote_line (subscription_plan_id)');
        $this->addSql('CREATE INDEX IDX_43F3EB7CD614C7E7 ON quote_line (price_id)');
        $this->addSql('COMMENT ON COLUMN quote_line.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quote_line.quote_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quote_line.subscription_plan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quote_line.price_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE webhook_endpoint (id UUID NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN webhook_endpoint.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE webhook_event (id UUID NOT NULL, type VARCHAR(255) NOT NULL, payload TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN webhook_event.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE webhook_event_response (id UUID NOT NULL, event_id UUID DEFAULT NULL, endpoint_id UUID DEFAULT NULL, url VARCHAR(255) NOT NULL, status_code INT DEFAULT NULL, body TEXT DEFAULT NULL, processing_time DOUBLE PRECISION NOT NULL, error_message VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FB4F885B71F7E88B ON webhook_event_response (event_id)');
        $this->addSql('CREATE INDEX IDX_FB4F885B21AF7E36 ON webhook_event_response (endpoint_id)');
        $this->addSql('COMMENT ON COLUMN webhook_event_response.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN webhook_event_response.event_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN webhook_event_response.endpoint_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF49395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote_subscription ADD CONSTRAINT FK_14273BD5DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote_subscription ADD CONSTRAINT FK_14273BD59A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote_line ADD CONSTRAINT FK_43F3EB7CDB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote_line ADD CONSTRAINT FK_43F3EB7C9B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote_line ADD CONSTRAINT FK_43F3EB7CD614C7E7 FOREIGN KEY (price_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE webhook_event_response ADD CONSTRAINT FK_FB4F885B71F7E88B FOREIGN KEY (event_id) REFERENCES webhook_event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE webhook_event_response ADD CONSTRAINT FK_FB4F885B21AF7E36 FOREIGN KEY (endpoint_id) REFERENCES webhook_endpoint (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE brand_settings ADD tax_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE brand_settings ADD tax_rate DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE brand_settings ADD digital_services_rate DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD tax_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD tax_exempt BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD tax_rate_digital DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD tax_rate_standard DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD type VARCHAR(255) NOT NULL DEFAULT \'business\'');
        $this->addSql('ALTER TABLE invoice RENAME COLUMN vat_total TO tax_total');
        $this->addSql('ALTER TABLE invoice_line ADD tax_percentage DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line ADD tax_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line ADD tax_country VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line RENAME COLUMN vat_total TO tax_total');
        $this->addSql('ALTER TABLE invoice_line RENAME COLUMN vat_percentage TO reverse_charge');
        $this->addSql('ALTER TABLE payment ADD invoice_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN payment.invoice_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6D28840D2989F1FD ON payment (invoice_id)');
        $this->addSql('ALTER TABLE payment_subscription DROP CONSTRAINT FK_C536D3C94C3A3BB');
        $this->addSql('ALTER TABLE payment_subscription DROP CONSTRAINT FK_C536D3C99A1887DC');
        $this->addSql('ALTER TABLE payment_subscription ADD CONSTRAINT FK_C536D3C94C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_subscription ADD CONSTRAINT FK_C536D3C99A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD tax_type VARCHAR(255) NOT NULL DEFAULT \'digital_goods\'');
        $this->addSql('ALTER TABLE product ADD tax_rate DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE receipt_payment DROP CONSTRAINT FK_7E6221F32B5CA896');
        $this->addSql('ALTER TABLE receipt_payment DROP CONSTRAINT FK_7E6221F34C3A3BB');
        $this->addSql('ALTER TABLE receipt_payment ADD CONSTRAINT FK_7E6221F32B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipt (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE receipt_payment ADD CONSTRAINT FK_7E6221F34C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE receipt_subscription DROP CONSTRAINT FK_32952A5C2B5CA896');
        $this->addSql('ALTER TABLE receipt_subscription DROP CONSTRAINT FK_32952A5C9A1887DC');
        $this->addSql('ALTER TABLE receipt_subscription ADD CONSTRAINT FK_32952A5C2B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipt (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE receipt_subscription ADD CONSTRAINT FK_32952A5C9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE settings ADD system_settings_invoice_number_generation VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD system_settings_subsequential_number INT DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD tax_settings_tax_customers_with_tax_numbers BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD tax_settings_european_business_tax_rules BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription_payment DROP CONSTRAINT FK_1E3D64969A1887DC');
        $this->addSql('ALTER TABLE subscription_payment DROP CONSTRAINT FK_1E3D64964C3A3BB');
        $this->addSql('ALTER TABLE subscription_payment ADD CONSTRAINT FK_1E3D64969A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_payment ADD CONSTRAINT FK_1E3D64964C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan ADD code_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription_plan ADD deleted BOOLEAN NOT NULL DEFAULT false');
        $this->addSql('ALTER TABLE subscription_plan ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EA664B6368C814C7 ON subscription_plan (code_name)');
        $this->addSql('ALTER TABLE subscription_plan_subscription_feature DROP CONSTRAINT FK_63CBB01D9B8CE200');
        $this->addSql('ALTER TABLE subscription_plan_subscription_feature DROP CONSTRAINT FK_63CBB01DA201F81C');
        $this->addSql('ALTER TABLE subscription_plan_subscription_feature ADD CONSTRAINT FK_63CBB01D9B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan_subscription_feature ADD CONSTRAINT FK_63CBB01DA201F81C FOREIGN KEY (subscription_feature_id) REFERENCES subscription_feature (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan_price DROP CONSTRAINT FK_5B8B27409B8CE200');
        $this->addSql('ALTER TABLE subscription_plan_price DROP CONSTRAINT FK_5B8B2740D614C7E7');
        $this->addSql('ALTER TABLE subscription_plan_price ADD CONSTRAINT FK_5B8B27409B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan_price ADD CONSTRAINT FK_5B8B2740D614C7E7 FOREIGN KEY (price_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE brand_settings ADD notification_settings_quote_created BOOLEAN DEFAULT NULL');

        $this->addSql("INSERT INTO templates
(id, \"name\", \"content\", brand)
VALUES('b17474b0-80ce-4331-858c-6c9b79ec7be2'::uuid, 'quote', '<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"utf-8\" />
        <title></title>
{% verbatim %}
        
        <style>
            .invoice-box {
                max-width: 800px;
                margin: auto;
                padding: 30px;
                border: 1px solid #eee;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
                font-size: 16px;
                line-height: 24px;
                font-family: ''Helvetica Neue'', ''Helvetica'', Helvetica, Arial, sans-serif;
                color: #555;
            }

            .invoice-box table {
                width: 100%;
                line-height: inherit;
                text-align: left;
            }

            .invoice-box table td {
                padding: 5px;
                vertical-align: top;
            }

            .invoice-box table tr td:nth-child(2) {
                text-align: right;
            }

            .invoice-box table tr.top table td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.top table td.title {
                font-size: 45px;
                line-height: 45px;
                color: #333;
            }

            .invoice-box table tr.information table td {
                padding-bottom: 40px;
            }

            .invoice-box table tr.heading td {
                background: #eee;
                border-bottom: 1px solid #ddd;
                font-weight: bold;
            }

            .invoice-box table tr.details td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.item td {
                border-bottom: 1px solid #eee;
            }

            .invoice-box table tr.item.last td {
                border-bottom: none;
            }

            .invoice-box table tr.total td:nth-child(2) {
                border-top: 2px solid #eee;
                font-weight: bold;
            }

            @media only screen and (max-width: 600px) {
                .invoice-box table tr.top table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }

                .invoice-box table tr.information table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }
            }

            /** RTL **/
            .invoice-box.rtl {
                direction: rtl;
                font-family: Tahoma, ''Helvetica Neue'', ''Helvetica'', Helvetica, Arial, sans-serif;
            }

            .invoice-box.rtl table {
                text-align: right;
            }

            .invoice-box.rtl table tr td:nth-child(2) {
                text-align: left;
            }
        </style>
{% endverbatim %}
    </head>

    <body>
        <div class=\"invoice-box\">
            <table cellpadding=\"0\" cellspacing=\"0\">
                <tr class=\"top\">
                    <td colspan=\"2\">
                        <strong>QUOTE</strong>
                    </td>
                </tr>
                <tr class=\"top\">
                    <td colspan=\"2\">
                         <table>
                            <tr>
                                <td class=\"title\">
                                    {{ brand.name }}
                                </td>

                                <td>
                                    Created: {{ quote.created_at }} <br />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>


                <tr class=\"heading\">
                    <td>Item</td>

                    <td>Price</td>
                </tr>

                {% for line in quote.lines %}
                <tr class=\"item\">
                    <td>{{ line.description }}</td>

                    <td>{{ line.total_display }}</td>
                </tr>
                {% endfor %}

                <tr class=\"total\">
                    <td></td>

                    <td>Total: {{ quote.total_display }}</td>
                </tr>
            </table>
        </div>
    </body>
</html>

{# MIT License

Copyright (c) 2021 Sparksuite

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the \"Software\"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED \"AS IS\", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE. #}
', 'default');
");

        $this->addSql("INSERT INTO email_templates
(id, brand_id, \"name\", locale, subject, use_emsp_template, template_id, template_body)
SELECT '4370990c-63eb-4fec-baa9-7daeba11407b'::uuid, b.id, 'quote_created', 'en', 'New Quote', false, NULL, '<html>
    <head>
      <title></title>
    </head>
    <body style=\"background: rgb(254,234,0);
background: radial-gradient(circle, rgba(254,234,0,1) 0%, rgba(246,156,0,1) 100%);; color: black;\">
    
    <div style=\"padding-top: 40px;\">
      <div style=\"margin:auto; background-color: white; max-width: 700px; padding: 50px; border-radius: 15px; margin-top: 40px; \">
        <h1 style=\"text-align:center;\"><img src=\"https://ha-static-data.s3.eu-central-1.amazonaws.com/github-readme-logo.png\" alt=\"{{ brand.name  }}\" /></h1>

    Your new quote is ready.
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
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF49395C3F3');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF4B03A8386');
        $this->addSql('ALTER TABLE quote_subscription DROP CONSTRAINT FK_14273BD5DB805178');
        $this->addSql('ALTER TABLE quote_subscription DROP CONSTRAINT FK_14273BD59A1887DC');
        $this->addSql('ALTER TABLE quote_line DROP CONSTRAINT FK_43F3EB7CDB805178');
        $this->addSql('ALTER TABLE quote_line DROP CONSTRAINT FK_43F3EB7C9B8CE200');
        $this->addSql('ALTER TABLE quote_line DROP CONSTRAINT FK_43F3EB7CD614C7E7');
        $this->addSql('ALTER TABLE webhook_event_response DROP CONSTRAINT FK_FB4F885B71F7E88B');
        $this->addSql('ALTER TABLE webhook_event_response DROP CONSTRAINT FK_FB4F885B21AF7E36');
        $this->addSql('DROP TABLE quote');
        $this->addSql('DROP TABLE quote_subscription');
        $this->addSql('DROP TABLE quote_line');
        $this->addSql('DROP TABLE webhook_endpoint');
        $this->addSql('DROP TABLE webhook_event');
        $this->addSql('DROP TABLE webhook_event_response');
        $this->addSql('DROP INDEX UNIQ_EA664B6368C814C7');
        $this->addSql('ALTER TABLE subscription_plan DROP code_name');
        $this->addSql('ALTER TABLE subscription_plan DROP deleted');
        $this->addSql('ALTER TABLE subscription_plan DROP deleted_at');
        $this->addSql('ALTER TABLE invoice_line DROP tax_percentage');
        $this->addSql('ALTER TABLE invoice_line DROP tax_type');
        $this->addSql('ALTER TABLE invoice_line DROP tax_country');
        $this->addSql('ALTER TABLE invoice_line RENAME COLUMN tax_total TO vat_total');
        $this->addSql('ALTER TABLE invoice_line RENAME COLUMN reverse_charge TO vat_percentage');
        $this->addSql('ALTER TABLE product DROP tax_type');
        $this->addSql('ALTER TABLE product DROP tax_rate');
        $this->addSql('ALTER TABLE payment_subscription DROP CONSTRAINT fk_c536d3c94c3a3bb');
        $this->addSql('ALTER TABLE payment_subscription DROP CONSTRAINT fk_c536d3c99a1887dc');
        $this->addSql('ALTER TABLE payment_subscription ADD CONSTRAINT fk_c536d3c94c3a3bb FOREIGN KEY (payment_id) REFERENCES payment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_subscription ADD CONSTRAINT fk_c536d3c99a1887dc FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan_price DROP CONSTRAINT fk_5b8b27409b8ce200');
        $this->addSql('ALTER TABLE subscription_plan_price DROP CONSTRAINT fk_5b8b2740d614c7e7');
        $this->addSql('ALTER TABLE subscription_plan_price ADD CONSTRAINT fk_5b8b27409b8ce200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plan (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan_price ADD CONSTRAINT fk_5b8b2740d614c7e7 FOREIGN KEY (price_id) REFERENCES price (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE receipt_subscription DROP CONSTRAINT fk_32952a5c2b5ca896');
        $this->addSql('ALTER TABLE receipt_subscription DROP CONSTRAINT fk_32952a5c9a1887dc');
        $this->addSql('ALTER TABLE receipt_subscription ADD CONSTRAINT fk_32952a5c2b5ca896 FOREIGN KEY (receipt_id) REFERENCES receipt (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE receipt_subscription ADD CONSTRAINT fk_32952a5c9a1887dc FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840D2989F1FD');
        $this->addSql('DROP INDEX IDX_6D28840D2989F1FD');
        $this->addSql('ALTER TABLE payment DROP invoice_id');
        $this->addSql('ALTER TABLE receipt_payment DROP CONSTRAINT fk_7e6221f32b5ca896');
        $this->addSql('ALTER TABLE receipt_payment DROP CONSTRAINT fk_7e6221f34c3a3bb');
        $this->addSql('ALTER TABLE receipt_payment ADD CONSTRAINT fk_7e6221f32b5ca896 FOREIGN KEY (receipt_id) REFERENCES receipt (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE receipt_payment ADD CONSTRAINT fk_7e6221f34c3a3bb FOREIGN KEY (payment_id) REFERENCES payment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice RENAME COLUMN tax_total TO vat_total');
        $this->addSql('ALTER TABLE settings DROP system_settings_invoice_number_generation');
        $this->addSql('ALTER TABLE settings DROP system_settings_subsequential_number');
        $this->addSql('ALTER TABLE settings DROP tax_settings_tax_customers_with_tax_numbers');
        $this->addSql('ALTER TABLE settings DROP tax_settings_european_business_tax_rules');
        $this->addSql('ALTER TABLE subscription_plan_subscription_feature DROP CONSTRAINT fk_63cbb01d9b8ce200');
        $this->addSql('ALTER TABLE subscription_plan_subscription_feature DROP CONSTRAINT fk_63cbb01da201f81c');
        $this->addSql('ALTER TABLE subscription_plan_subscription_feature ADD CONSTRAINT fk_63cbb01d9b8ce200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plan (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan_subscription_feature ADD CONSTRAINT fk_63cbb01da201f81c FOREIGN KEY (subscription_feature_id) REFERENCES subscription_feature (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE brand_settings DROP tax_number');
        $this->addSql('ALTER TABLE brand_settings DROP tax_rate');
        $this->addSql('ALTER TABLE brand_settings DROP digital_services_rate');
        $this->addSql('ALTER TABLE subscription_payment DROP CONSTRAINT fk_1e3d64969a1887dc');
        $this->addSql('ALTER TABLE subscription_payment DROP CONSTRAINT fk_1e3d64964c3a3bb');
        $this->addSql('ALTER TABLE subscription_payment ADD CONSTRAINT fk_1e3d64969a1887dc FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_payment ADD CONSTRAINT fk_1e3d64964c3a3bb FOREIGN KEY (payment_id) REFERENCES payment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customers DROP tax_number');
        $this->addSql('ALTER TABLE customers DROP tax_exempt');
        $this->addSql('ALTER TABLE customers DROP tax_rate_digital');
        $this->addSql('ALTER TABLE customers DROP tax_rate_standard');
        $this->addSql('ALTER TABLE customers DROP type');
        $this->addSql('ALTER TABLE brand_settings DROP notification_settings_quote_created');
    }
}
