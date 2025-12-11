<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211111410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'A broken migration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("DROP TABLE broken_migration");
    }

    public function down(Schema $schema): void
    {
    }
}
