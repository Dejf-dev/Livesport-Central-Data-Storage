<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250412141836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creating entire matches database structure';
    }

    public function up(Schema $schema): void
    {
        // team
        $this->addSql('
            CREATE SEQUENCE team_team_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
        ');
        $this->addSql('
            CREATE TABLE team (
                                team_id BIGINT
                                     PRIMARY KEY
                                        DEFAULT nextval(\'team_team_id_seq\'::regclass),
                                name VARCHAR(100) NOT NULL,
                                city VARCHAR(100) NOT NULL,
                                founded SMALLINT NOT NULL,
                                stadium VARCHAR(100) NOT NULL
                                        );
        ');

        // match
        $this->addSql('
            CREATE SEQUENCE match_match_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
        ');
        $this->addSql('
            CREATE TABLE match (
                                match_id BIGINT
                                     PRIMARY KEY
                                        DEFAULT nextval(\'match_match_id_seq\'::regclass),
                                match_date TIMESTAMP(0) WITH TIME ZONE NOT NULL,
                                stadium VARCHAR(100) NOT NULL,
                                score_home SMALLINT NOT NULL,
                                score_away SMALLINT NOT NULL,
                                home_team_id BIGINT NOT NULL 
                                    CONSTRAINT fk_match_home_team 
                                        REFERENCES team ON DELETE CASCADE,
                                away_team_id BIGINT NOT NULL 
                                    CONSTRAINT fk_match_away_team 
                                        REFERENCES team ON DELETE CASCADE        
                                        );
        ');
        $this->addSql('COMMENT ON COLUMN match.match_date IS \'(DC2Type:datetimetz_immutable)\'');

        // event
        $this->addSql('
            CREATE SEQUENCE event_event_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
        ');
        $this->addSql('
            CREATE TABLE event (
                                event_id BIGINT
                                     PRIMARY KEY
                                        DEFAULT nextval(\'event_event_id_seq\'::regclass),
                                player VARCHAR(100) NOT NULL,
                                event_type VARCHAR(30) NOT NULL,
                                minute SMALLINT NOT NULL,
                                team_id BIGINT NOT NULL 
                                    CONSTRAINT fk_event_team 
                                        REFERENCES team ON DELETE CASCADE,
                                match_id BIGINT NOT NULL 
                                    CONSTRAINT fk_event_match 
                                        REFERENCES match ON DELETE CASCADE        
                                        );
        ');

        // inserting teams
        $this->addSql('INSERT INTO team (name, city, founded, stadium) VALUES (\'Manchester United\', \'Manchester\', 1878, \'Old Trafford\')');
        $this->addSql('INSERT INTO team (name, city, founded, stadium) VALUES (\'FC Barcelona\', \'Barcelona\', 1899, \'Camp Nou\')');
        $this->addSql('INSERT INTO team (name, city, founded, stadium) VALUES (\'Real Madrid\', \'Madrid\', 1902, \'Santiago Bernabéu\')');
        $this->addSql('INSERT INTO team (name, city, founded, stadium) VALUES (\'Bayern Munich\', \'Munich\', 1900, \'Allianz Arena\')');
        $this->addSql('INSERT INTO team (name, city, founded, stadium) VALUES (\'Juventus\', \'Turin\', 1897, \'Allianz Stadium\')');
        $this->addSql('INSERT INTO team (name, city, founded, stadium) VALUES (\'Paris Saint-Germain\', \'Paris\', 1970, \'Parc des Princes\')');
        $this->addSql('INSERT INTO team (name, city, founded, stadium) VALUES (\'Liverpool FC\', \'Liverpool\', 1892, \'Anfield\')');
        $this->addSql('INSERT INTO team (name, city, founded, stadium) VALUES (\'AC Milan\', \'Milan\', 1899, \'San Siro\')');
        $this->addSql('INSERT INTO team (name, city, founded, stadium) VALUES (\'Arsenal FC\', \'London\', 1886, \'Emirates Stadium\')');
        $this->addSql('INSERT INTO team (name, city, founded, stadium) VALUES (\'Chelsea FC\', \'London\', 1905, \'Stamford Bridge\')');

        // inserting matches
        $this->addSql('INSERT INTO match (match_date, stadium, score_home, score_away, home_team_id, away_team_id) 
                VALUES (\'2025-04-15 20:00:00+00\', \'Wembley Stadium\', 3, 1, 1, 2)');

        $this->addSql('INSERT INTO match (match_date, stadium, score_home, score_away, home_team_id, away_team_id) 
                VALUES (\'2025-04-16 18:30:00+00\', \'Camp Nou\', 2, 2, 2, 3)');

        $this->addSql('INSERT INTO match (match_date, stadium, score_home, score_away, home_team_id, away_team_id) 
                VALUES (\'2025-04-17 21:00:00+00\', \'Millennium Stadium\', 4, 0, 3, 4)');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('COMMENT ON COLUMN match.match_date IS NULL');

        $this->addSql('DROP TABLE IF EXISTS event CASCADE');
        $this->addSql('DROP TABLE IF EXISTS match CASCADE');
        $this->addSql('DROP TABLE IF EXISTS team CASCADE');

        $this->addSql('DROP SEQUENCE event_event_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE match_match_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE team_team_id_seq CASCADE');
    }
}
