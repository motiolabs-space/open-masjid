<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRegionsTables extends Migration
{
    public function up()
    {
        // Migration tables creation commented out due to structural differences with database.sql
        // The database.sql uses VARCHAR(50) for regencies.id while migration uses INT(11)
        // This causes conflicts when tables already exist from database.sql import
        // Table structures must be consistent, so we're temporarily disabling the migration approach
        // and relying on database.sql structure instead
        /*
        // Check if tables already exist and only create if they don't
        $this->db->query("CREATE TABLE IF NOT EXISTS provinces (id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, name VARCHAR(100) NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB");
        $this->db->query("CREATE TABLE IF NOT EXISTS regencies (id INT(11) UNSIGNED NOT NULL, province_id INT(11) UNSIGNED NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY (id), FOREIGN KEY (province_id) REFERENCES provinces(id))");
        */

        // Drop tables if they exist - keeping the drop functionality
        $this->db->query("DROP TABLE IF EXISTS regencies");
        $this->db->query("DROP TABLE IF EXISTS provinces");
    }

    public function down()
    {
        // Table dropping functionality preserved for rollback purposes
        // Drop tables if they exist
        $this->db->query("DROP TABLE IF EXISTS regencies");
        $this->db->query("DROP TABLE IF EXISTS provinces");
    }
}


