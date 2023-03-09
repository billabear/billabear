<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220922203335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forgot_password_code (id UUID NOT NULL, user_id UUID DEFAULT NULL, code VARCHAR(255) NOT NULL, used BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, used_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B30A7571A76ED395 ON forgot_password_code (user_id)');
        $this->addSql('COMMENT ON COLUMN forgot_password_code.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN forgot_password_code.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE invite_codes (id UUID NOT NULL, user_id UUID DEFAULT NULL, invited_user_id UUID DEFAULT NULL, code VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, used BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, used_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, cancelled BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E8D89FB2A76ED395 ON invite_codes (user_id)');
        $this->addSql('CREATE INDEX IDX_E8D89FB2C58DAD6E ON invite_codes (invited_user_id)');
        $this->addSql('COMMENT ON COLUMN invite_codes.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invite_codes.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invite_codes.invited_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE notification (id UUID NOT NULL, message_template VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, read_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_read BOOLEAN NOT NULL, link_url_name VARCHAR(255) NOT NULL, link_url_variables JSON NOT NULL, link_is_raw BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN notification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE parthenon_ab_experiment_variant (id UUID NOT NULL, experiment_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, percentage INT NOT NULL, is_default BOOLEAN NOT NULL, stats_number_of_sessions INT NOT NULL, stats_number_of_conversions INT NOT NULL, stats_conversion_percentage DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_284E93BDFF444C8 ON parthenon_ab_experiment_variant (experiment_id)');
        $this->addSql('CREATE INDEX search_idx ON parthenon_ab_experiment_variant (name)');
        $this->addSql('COMMENT ON COLUMN parthenon_ab_experiment_variant.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN parthenon_ab_experiment_variant.experiment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE parthenon_ab_experiments (id UUID NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, desired_result VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, activated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_deleted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6C53D6955E237E06 ON parthenon_ab_experiments (name)');
        $this->addSql('COMMENT ON COLUMN parthenon_ab_experiments.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE team_invite_codes (id UUID NOT NULL, user_id UUID DEFAULT NULL, invited_user_id UUID DEFAULT NULL, team_id UUID DEFAULT NULL, code VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, used BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, used_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, cancelled BOOLEAN NOT NULL, role VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E05E9270A76ED395 ON team_invite_codes (user_id)');
        $this->addSql('CREATE INDEX IDX_E05E9270C58DAD6E ON team_invite_codes (invited_user_id)');
        $this->addSql('CREATE INDEX IDX_E05E9270296CD8AE ON team_invite_codes (team_id)');
        $this->addSql('COMMENT ON COLUMN team_invite_codes.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN team_invite_codes.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN team_invite_codes.invited_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN team_invite_codes.team_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE teams (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, subscription_price_id VARCHAR(255) DEFAULT NULL, subscription_plan_name VARCHAR(255) DEFAULT NULL, subscription_payment_schedule VARCHAR(255) DEFAULT NULL, subscription_active BOOLEAN DEFAULT NULL, subscription_status VARCHAR(255) DEFAULT NULL, subscription_started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subscription_valid_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subscription_updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subscription_ended_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subscription_payment_id VARCHAR(255) DEFAULT NULL, subscription_customer_id VARCHAR(255) DEFAULT NULL, subscription_checkout_id VARCHAR(255) DEFAULT NULL, subscription_seats INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN teams.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE users (id UUID NOT NULL, team_id UUID DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, confirmation_code VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, activated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deactivated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_confirmed BOOLEAN NOT NULL, is_deleted BOOLEAN NOT NULL, roles TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1483A5E9296CD8AE ON users (team_id)');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN users.team_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN users.roles IS \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE forgot_password_code ADD CONSTRAINT FK_B30A7571A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invite_codes ADD CONSTRAINT FK_E8D89FB2A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invite_codes ADD CONSTRAINT FK_E8D89FB2C58DAD6E FOREIGN KEY (invited_user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE parthenon_ab_experiment_variant ADD CONSTRAINT FK_284E93BDFF444C8 FOREIGN KEY (experiment_id) REFERENCES parthenon_ab_experiments (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_invite_codes ADD CONSTRAINT FK_E05E9270A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_invite_codes ADD CONSTRAINT FK_E05E9270C58DAD6E FOREIGN KEY (invited_user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_invite_codes ADD CONSTRAINT FK_E05E9270296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE forgot_password_code DROP CONSTRAINT FK_B30A7571A76ED395');
        $this->addSql('ALTER TABLE invite_codes DROP CONSTRAINT FK_E8D89FB2A76ED395');
        $this->addSql('ALTER TABLE invite_codes DROP CONSTRAINT FK_E8D89FB2C58DAD6E');
        $this->addSql('ALTER TABLE parthenon_ab_experiment_variant DROP CONSTRAINT FK_284E93BDFF444C8');
        $this->addSql('ALTER TABLE team_invite_codes DROP CONSTRAINT FK_E05E9270A76ED395');
        $this->addSql('ALTER TABLE team_invite_codes DROP CONSTRAINT FK_E05E9270C58DAD6E');
        $this->addSql('ALTER TABLE team_invite_codes DROP CONSTRAINT FK_E05E9270296CD8AE');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E9296CD8AE');
        $this->addSql('DROP TABLE forgot_password_code');
        $this->addSql('DROP TABLE invite_codes');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE parthenon_ab_experiment_variant');
        $this->addSql('DROP TABLE parthenon_ab_experiments');
        $this->addSql('DROP TABLE team_invite_codes');
        $this->addSql('DROP TABLE teams');
        $this->addSql('DROP TABLE users');
    }
}
