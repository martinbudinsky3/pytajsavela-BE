<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tagNames = ['2015-16', '2016-17', '2017-18', '2018-19', '2019-20', '2020-21', 
                     'bakalárske-štúdium', 'inžinierske-štúdium', 'doktorandské-štúdium',
                     'prednáška', 'cvičenie', 'projekt', 'vedenie', 'výučba', 'ais',
                     'ma', 'prpr', 'adm', 'aj', 'mip', 'ppi', 'oop', 'fyz', 'ml', 'pam', 'tziv',
                     'tk', 'os', 'dsa', 'pks', 'pas', 'pikt', 'dbs', 'psi', 'ui', 'aza', 'vava',
                     'wtech', 'paralpr', 'iau', 'ičp', 'apc', 'ppgso', 'vavjs', 'teap', 'mtaa',
                     'pis', 'dm', 'alg', 'anu', 'aass', 'aps', 'ass', 'aovs', 'bks', 'bit', 'bos',
                     'bvi', 'ddss', 'dmblock', 'dovi', 'fman', 'faps', 'flp', 'gra', 'ivzdel', 'ift',
                     'kod', 'kss', 'kpais', 'me', 'mbvit', 'bp1', 'bp2', 'dp0', 'dp1', 'dp2', 'dp3'];

        foreach($tagNames as $tagName) {
            Tag::create([
                'name' => $tagName,
            ]);
        }
    }
}
