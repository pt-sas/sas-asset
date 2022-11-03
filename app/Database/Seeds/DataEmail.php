<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DataEmail extends Seeder
{
    public function run()
    {
        $email = [
            'created_by'    => 1,
            'updated_by'    => 1,
            'protocol'      => 'smtp',
            'smtphost'      => 'mail.sahabatabadi.com',
            'smtpport'      => 465,
            'smtpcrypto'    => 'ssl',
            'smtpuser'      => 'alert.sas@sahabatabadi.com',
            'smtppassword'  => 'superIT2022@jkt1',
            'requestemail'  => 'alert.sas@sahabatabadi.com'
        ];

        $this->db->table('sys_email')->insert($email);
    }
}
