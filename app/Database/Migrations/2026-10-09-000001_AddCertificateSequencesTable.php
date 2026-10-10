<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel certificate_sequences — menyimpan nomor urut sertifikat per tahun.
 * Dipakai CertificateSequenceModel::nextForYear() pada penerbitan sertifikat
 * untuk menghasilkan certificate_number yang UNIQUE dengan format
 * SERT/{tahun}/{nomor urut 5 digit}.
 *
 * Sebelumnya nomor sertifikat dihitung via MAX(SUBSTRING_INDEX(...)) +
 * UPDATE last_number, tetapi pendekatan itu racy dan sudah dihapus.
 */
class AddCertificateSequencesTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('certificate_sequences')) {
            return;
        }

        $this->forge->addField([
            'year'        => ['type' => 'SMALLINT', 'unsigned' => true],
            'last_number' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'created_at'  => ['type' => 'DATETIME'],
            'updated_at'  => ['type' => 'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('year');
        $this->forge->createTable('certificate_sequences', true);
    }

    public function down()
    {
        if ($this->db->tableExists('certificate_sequences')) {
            $this->forge->dropTable('certificate_sequences', true);
        }
    }
}