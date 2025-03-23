<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250323124245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Increase payment columns length to accommodate encrypted data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment MODIFY card_number VARCHAR(1024) NOT NULL');
        $this->addSql('ALTER TABLE payment MODIFY expiration_date VARCHAR(1024) NOT NULL');
        $this->addSql('ALTER TABLE payment MODIFY cvv VARCHAR(1024) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment MODIFY card_number VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE payment MODIFY expiration_date VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE payment MODIFY cvv VARCHAR(255) NOT NULL');
    }
}
