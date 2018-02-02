<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCampaignDetailViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the "view_active_campaign_ids" view.
        DB::statement("CREATE VIEW `view_active_campaign_ids` AS SELECT `campaign_attributes`.`campaign_id` AS `campaign_id` FROM (`campaign_attributes` left join `mutable_data_pairs` on((`campaign_attributes`.`storage_id` = `mutable_data_pairs`.`id`))) where ((`campaign_attributes`.`name` = 'campaign_status') and (`mutable_data_pairs`.`string_value` = 'live'));");
        // Create the "view_campaign_attribute_values" view.
        DB::statement("CREATE VIEW `view_campaign_attribute_values` AS SELECT `campaign_attributes`.`campaign_id` AS `campaign_id`,`campaign_attributes`.`name` AS `name`,ifnull(`mutable_data_pairs`.`json_value`,`mutable_data_pairs`.`string_value`) AS `value` FROM (`campaign_attributes` left join `mutable_data_pairs` on((`campaign_attributes`.`storage_id` = `mutable_data_pairs`.`id`)));");
        // Create the "view_full_campaigns" view.
        DB::statement("CREATE VIEW view_full_campaigns AS
select c.id,c.name,c.advertiser_id,c.campaign_type_id,c.posting_url,c.created_at,c.updated_at,(cav_cpl.value) as cpl,(cav_dc.value) as daily_cap,(cav_status.value) as campaign_status from campaigns c left join view_campaign_attribute_values cav_cpl on c.id = cav_cpl.campaign_id and cav_cpl.name = \"cpl\" left join view_campaign_attribute_values cav_dc on c.id = cav_dc.campaign_id and cav_dc.name = \"daily_cap\" left join view_campaign_attribute_values cav_status on c.id = cav_status.campaign_id and cav_status.name = \"campaign_status\";");
        // Create the "view_full_live_campaign_values" view.
        DB::statement("CREATE  VIEW `view_full_live_campaign_value` AS SELECT `view_full_campaigns`.`name` AS `name`,round((`view_full_campaigns`.`cpl` * `view_full_campaigns`.`daily_cap`),2) AS `potential_value`, `view_full_campaigns`.`campaign_status` AS `campaign_status` FROM `view_full_campaigns`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
