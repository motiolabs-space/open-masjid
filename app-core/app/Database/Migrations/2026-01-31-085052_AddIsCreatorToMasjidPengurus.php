<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsCreatorToMasjidPengurus extends Migration
{
    public function up()
    {
        // The `is_creator` column is already defined in `database.sql`.
        // Commenting out to prevent "Duplicate column name 'is_creator'" error.
        // $this->forge->addColumn('masjid_pengurus', [
        //     'is_creator' => [
        //         'type'       => 'TINYINT',
        //         'constraint' => 1,
        //         'default'    => 0,
        //         'after'      => 'title'
        //     ],
        // ]);
    }

    public function down()
    {
        $this->forge->dropColumn('masjid_pengurus', 'is_creator');
    }
}
