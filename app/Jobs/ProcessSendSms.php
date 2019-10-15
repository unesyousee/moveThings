<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Kavenegar\KavenegarApi;

class ProcessSendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $smsContent;
    private $token = "3438453342712B584C6A524E6D68615270772F4636617056765A766957554E30";
    private $templates = [
        866 => 'verification',
        3208 => 'storedNewOrder',
        3678 => 'driverAccept',
        4182 => 'اپلیکیشنهای نوبار کافه بازار برای اندروید: [Android] سیب اپ برای آیفون: [Ios] درخواست روی سایت برای تمام دستگاه‌ها: [Website] ',
        4183 => 'alertToUserOderStored',
        4642 => 'alertToUserOderStored',
        5424 => 'orderUpdated',
        5822 => 'checkoutByCredit',
        7418 => '',
        8350 => 'alertDetailToUserOderStored',
        1 => 'acceptOrder'
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($smsContent)
    {
        $this->smsContent = $smsContent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (env('sms', false) == "kavenegar") {
            return $this->kavenegarHandler();
        }
        else {
            sendSms($this->smsContent['phone'], $this->smsContent['templateId'], $this->smsContent['parameterArray']);
        }
    }
    private function kavenegarHandler()
    {
        $sender = "10007000700222";
        $api = new KavenegarApi($this->token);
        $receptor = $this->smsContent['phone'];
        $token = isset($this->smsContent['parameterArray'][0]['ParameterValue']) ? str_replace( ' ','_' , $this->smsContent['parameterArray'][0]['ParameterValue'] ) : null;
        $token2 = isset($this->smsContent['parameterArray'][1]['ParameterValue']) ? str_replace( ' ','_' , $this->smsContent['parameterArray'][1]['ParameterValue'] ) : null;
        $token3 = isset($this->smsContent['parameterArray'][2]['ParameterValue']) ? str_replace( ' ','_' , $this->smsContent['parameterArray'][2]['ParameterValue'] ) : null;
        $template = $this->templates[$this->smsContent['templateId']];
        Log::info($token . ' ' . $token2 . ' ' . $token3 );
        $response = $api->VerifyLookup($receptor,$token,$token2,$token3,$template);
//dump($token, $token2, $token3, $template);
    }

}
