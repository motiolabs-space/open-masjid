<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTargetDonationToPrograms extends Migration
{
    public function up()
    {
        // The `target_donation` column is already defined in `database.sql`.
        // Commenting out to prevent "Duplicate column name `target_donation`" error.
        /*
        $this->forge->addColumn('masjid_programs', [
            'target_donation' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'after'      => 'quota'
            ]
        ]);
        */
    }

    public function down()
    {
        $this->forge->dropColumn('masjid_programs', 'target_donation');
    }
}
