<?php

namespace Database\Seeders;

use App\Models\Plot;
use App\Models\Site;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $site=Site::create([
            'name'          => "Area 44",
            "plot_width"    => 30,
            "plot_height"   => 40,
            "plot_price"    => 3000000,
            "location"      => "Near State House",
            "district"      => "Lilongwe",
            "layout"        => "assets/files/area_44_layout.pdf",
        ]);
        $this->generatePlots($site, "44");

        $site=Site::create([
            'name'          => "Area 54",
            "plot_width"    => 30,
            "plot_height"   => 15,
            "plot_price"    => 750000,
            "location"      => "Near Lumbadzi",
            "district"      => "Lilongwe",
            "layout"        => "assets/files/area_44_layout.pdf",
        ]);
        $this->generatePlots($site, "54");

        $site=Site::create([
            'name'          => "Njewa",
            "plot_width"    => 30,
            "plot_height"   => 15,
            "plot_price"    => 1500000,
            "location"      => null,
            "district"      => "Lilongwe",
            "layout"        => "assets/files/area_44_layout.pdf",
        ]);
        $this->generatePlots($site, "58");
    }

    public function generatePlots(Site $site, $leading)
    {
        $numberOfPlots = 100;
        for ($index = 1; $index <= $numberOfPlots; $index++){
            Plot::create([
                "name"=> $leading . "/" . $index,
                "status"=>1,
                "hectare"=>fake()->randomFloat(5,0.4,1),
                "site_id"=>$site->id,
            ]);
        }
    }
}
