<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTargetDonationToPrograms extends Migration
{
    public function up()
    {
        $this->forge->addColumn('masjid_programs', [
            'target_donation' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'after'      => 'quota'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('masjid_programs', 'target_donation');
    }
}
