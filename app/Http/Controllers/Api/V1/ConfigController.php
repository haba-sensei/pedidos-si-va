<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\Currency;

class ConfigController extends Controller
{
    public function configuration()
    {
        $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        $cod = json_decode(BusinessSetting::where(['key' => 'cash_on_delivery'])->first()->value, true);
        $dp = json_decode(BusinessSetting::where(['key' => 'digital_payment'])->first()->value, true);
        return response()->json([
            'restaurant_name' => BusinessSetting::where(['key' => 'restaurant_name'])->first()->value,
            'restaurant_open_time' => BusinessSetting::where(['key' => 'restaurant_open_time'])->first()->value,
            'restaurant_close_time' => BusinessSetting::where(['key' => 'restaurant_close_time'])->first()->value,
            'restaurant_logo' => BusinessSetting::where(['key' => 'logo'])->first()->value,
            'restaurant_address' => BusinessSetting::where(['key' => 'address'])->first()->value,
            'restaurant_phone' => BusinessSetting::where(['key' => 'phone'])->first()->value,
            'restaurant_email' => BusinessSetting::where(['key' => 'email_address'])->first()->value,
            'restaurant_location_coverage' => Branch::where(['id'=>1])->first(['longitude','latitude','coverage']),
            'minimum_order_value' => (float)BusinessSetting::where(['key' => 'minimum_order_value'])->first()->value,
            'base_urls' => [
                'product_image_url' => asset('storage/app/public/product'),
                'customer_image_url' => asset('storage/app/public/profile'),
                'banner_image_url' => asset('storage/app/public/banner'),
                'category_image_url' => asset('storage/app/public/category'),
                'review_image_url' => asset('storage/app/public/review'),
                'notification_image_url' => asset('storage/app/public/notification'),
                'restaurant_image_url' => asset('storage/app/public/restaurant'),
                'delivery_man_image_url' => asset('storage/app/public/delivery-man'),
                'chat_image_url' => asset('storage/app/public/conversation'),
            ],
            'currency_symbol' => $currency_symbol,
            'delivery_charge' => BusinessSetting::where(['key' => 'delivery_charge'])->first()->value,
            'cash_on_delivery' => $cod['status'] == 1 ? 'true' : 'false',
            'digital_payment' => $dp['status'] == 1 ? 'true' : 'false',
            'branches' => Branch::all(['id','name','email','longitude','latitude','address','coverage']),
            'terms_and_conditions' => BusinessSetting::where(['key' => 'terms_and_conditions'])->first()->value
        ]);
    }
}
