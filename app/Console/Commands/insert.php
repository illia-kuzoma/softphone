<?php

namespace App\Console\Commands;

use App\Models\ReportMissedCall;
use Illuminate\Console\Command;

class insert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $missedCalls = new ReportMissedCall();
        $data = json_decode('[{"id":"1602133000117129231","type":null,"first_name":"1602133000117129231","last_name":"1602133000117129231","phone":"Missed call from Game 7 Physical Therapy (7)","contact":"Greg Zbrizher","priority":null,"business_name":"Game 7 Physical Therapy","time_start":"2020-01-31T15:18:00-05:00","user_id":"1602133000000178605"},{"id":"1602133000117129194","type":null,"first_name":"1602133000117129194","last_name":"1602133000117129194","phone":"Missed call from +15704684598","contact":"Greg Zbrizher","priority":null,"business_name":null,"time_start":"2020-01-31T14:08:00-05:00","user_id":"1602133000000178605"},{"id":"1602133000117129163","type":null,"first_name":"1602133000117129163","last_name":"1602133000117129163","phone":"Missed call from +15167945844","contact":"Greg Zbrizher","priority":null,"business_name":null,"time_start":"2020-01-31T13:33:00-05:00","user_id":"1602133000000178605"},{"id":"1602133000117129143","type":null,"first_name":"1602133000117129143","last_name":"1602133000117129143","phone":"Missed call from +19728009194","contact":"Laura Warchuk","priority":null,"business_name":null,"time_start":"2020-01-31T13:04:00-05:00","user_id":"1602133000097611001"},{"id":"1602133000117129109","type":null,"first_name":"1602133000117129109","last_name":"1602133000117129109","phone":"Missed call from Game 7 Physical Therapy (7)","contact":"Greg Zbrizher","priority":null,"business_name":"Game 7 Physical Therapy","time_start":"2020-01-31T12:04:00-05:00","user_id":"1602133000000178605"},{"id":"1602133000117096121","type":null,"first_name":"1602133000117096121","last_name":"1602133000117096121","phone":"Missed call from +17052884901","contact":"Greg Zbrizher","priority":null,"business_name":null,"time_start":"2020-01-31T13:52:00-05:00","user_id":"1602133000000178605"},{"id":"1602133000117096070","type":null,"first_name":"1602133000117096070","last_name":"1602133000117096070","phone":"Missed call from Suzanne Borgioli (+19782574363)","contact":"Greg Zbrizher","priority":null,"business_name":"Giving Tree Yoga & Wellness","time_start":"2020-01-31T12:01:00-05:00","user_id":"1602133000000178605"},{"id":"1602133000117092181","type":null,"first_name":"1602133000117092181","last_name":"1602133000117092181","phone":"Missed call from +12896543760","contact":"Greg Zbrizher","priority":null,"business_name":null,"time_start":"2020-01-31T17:41:00-05:00","user_id":"1602133000000178605"},{"id":"1602133000117092088","type":null,"first_name":"1602133000117092088","last_name":"1602133000117092088","phone":"Missed call from Quincy Dickens (+18563577133)","contact":"Greg Zbrizher","priority":null,"business_name":"Start Up Nutrition & Fitness","time_start":"2020-01-31T12:41:00-05:00","user_id":"1602133000000178605"},{"id":"1602133000117091265","type":null,"first_name":"1602133000117091265","last_name":"1602133000117091265","phone":"Missed call from +12149013550","contact":"Greg Zbrizher","priority":null,"business_name":null,"time_start":"2020-01-31T17:16:00-05:00","user_id":"1602133000000178605"},{"id":"1602133000117091163","type":null,"first_name":"1602133000117091163","last_name":"1602133000117091163","phone":"Missed call from +16467679237","contact":"Greg Zbrizher","priority":null,"business_name":null,"time_start":"2020-01-31T15:26:00-05:00","user_id":"1602133000000178605"},{"id":"1602133000117091113","type":null,"first_name":"1602133000117091113","last_name":"1602133000117091113","phone":"Missed call from +15704684598","contact":"Greg Zbrizher","priority":null,"business_name":null,"time_start":"2020-01-31T14:09:00-05:00","user_id":"1602133000000178605"}]', true);
      # print_r($data);exit;
        $missedCalls->insert($data);
    }
}
