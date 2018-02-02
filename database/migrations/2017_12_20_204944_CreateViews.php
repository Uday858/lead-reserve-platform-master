'<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_lead_statuses`
AS SELECT
   json_extract(`platform_events`.`json_value`,'$.lead_id') AS `lead_id`,(`platform_events`.`name` = 'lead.test') AS `is_test`,(`platform_events`.`name` = 'lead.accepted') AS `is_accepted`,(`platform_events`.`name` = 'lead.rejected') AS `is_rejected`
FROM `platform_events` where ((`platform_events`.`name` = 'lead.accepted') or (`platform_events`.`name` = 'lead.rejected') or (`platform_events`.`name` = 'lead.test'));");
        DB::statement("CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_lead_request_response`
AS SELECT
   cast(json_extract(`pe`.`json_value`,'$.lead_id') as unsigned) AS `lead_id`,cast(json_extract(`pe`.`json_value`,'$.campaign_id') as unsigned) AS `campaign_id`,cast(json_extract(`pe`.`json_value`,'$.publisher_id') as unsigned) AS `publisher_id`,
   `pe`.`name` AS `name`,json_extract(`pe`.`json_value`,'$.requestURL') AS `request`,json_extract(`pe`.`json_value`,'$.response_data.contents') AS `response`,
   `pe`.`created_at` AS `created_at`
FROM `platform_events` `pe` where ((`pe`.`name` = 'lead.sent') or (`pe`.`name` = 'lead.presend'));");
        DB::statement("CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_full_lead_explorer_values`
AS SELECT
   `lrr`.`lead_id` AS `lead_id`,
   `lrr`.`campaign_id` AS `campaign_id`,
   `lrr`.`publisher_id` AS `publisher_id`,
   `lrr`.`name` AS `name`,
   `lrr`.`request` AS `request`,
   `lrr`.`response` AS `response`,
   `ls`.`is_test` AS `is_test`,
   `ls`.`is_accepted` AS `is_accepted`,
   `ls`.`is_rejected` AS `is_rejected`,
   `lrr`.`created_at` AS `created_at`
FROM (`view_lead_request_response` `lrr` join `view_lead_statuses` `ls` on((`lrr`.`lead_id` = `ls`.`lead_id`)));");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW view_lead_statuses;");
        DB::statement("DROP VIEW view_full_lead_explorer_values;");
        DB::statement("DROP VIEW view_lead_request_response;");
    }
}
