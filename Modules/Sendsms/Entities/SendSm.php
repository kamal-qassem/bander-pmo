<?php
namespace Modules\Sendsms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

//use Tzsk\Sms\Facade\Sms;
//use Tzsk\Sms\SmsManager;

use Twilio\Rest\Client;

/**
 * Class SendSm
 *
 * @package App
 * @property string $send_to
 * @property text $message
 * @property string $gateway
*/
class SendSm extends Model
{
    use SoftDeletes;

    protected $fillable = ['send_to', 'message', 'gateway_id', 'status', 'gateway_response'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        SendSm::observe(new \App\Observers\UserActionsObserver);
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setGatewayIdAttribute($input)
    {
        $this->attributes['gateway_id'] = $input ? $input : null;
    }
    
    public function gateway()
    {
        return $this->belongsTo(SmsGateway::class, 'gateway_id')->withTrashed();
    }

    /**
     * Common email function to send emails
     * @param  [type] $template [key of the template]
     * @param  [type] $data     [data to be passed to view]
     * @return [type]           [description]
     */
    public static function sendSms( $data )
    { 
                
        $default_sms_gateway = getSetting( 'default_sms_gateway', 'site_settings', '');
        $return = array(
                'status' => 'success',
                'message' => trans( 'custom.smstemplates.message-sent' ),
            );
        $default_sms_gateway = 'plivo';
        if ( ! empty( $default_sms_gateway )) {
            $config = array(
                'sid' => '',
                'token' => '',
                'from' => '',
            );

            
            if ( 'twilio' === $default_sms_gateway ) {
                $config = array(
                    'sid' => getSetting( 'TWILIO_SID', 'twilio', ''),
                    'token' => getSetting( 'TWILIO_TOKEN_EDIT', 'twilio', ''),
                    'from' => getSetting( 'TWILIO_FROM', 'twilio', ''),
                );
                $twilio = new Client($config['sid'], $config['token']);
                $res = $twilio->messages->create(
                    // Where to send a text message (your cell phone?)
                    '+' . $data['tonumber'],
                    array(
                        'from' => $config['from'],
                        'body' => $data['content'],
                    )
                );

                if ( ! empty( $res['payload']['status'] ) && 'failed' === $res['payload']['status'] ) {
                    $return['status'] = 'failed';
                    $return['message'] = $res['payload']['message'];
                }
            } elseif( 'nexmo' === $default_sms_gateway ) {
                $api_key = getSetting( 'NEXMO_API_KEY', 'nexmo', '');
                $secret_key = getSetting( 'NEXMO_API_SECRET', 'nexmo', '');

                if ( ! empty( $api_key ) && ! empty( $secret_key ) ) {
                    $client = new \Nexmo\Client(new \Nexmo\Client\Credentials\Basic($api_key, $secret_key));
                    
                    $data['tonumber'] = '+' . $data['tonumber'];
                    $message = $client->message()->send([
                        'to' => $data['tonumber'],
                        'from' => 'Acme Inc',
                        'text' => $data['content']
                    ]);

                    if ( isset( $message['status'] ) && $message['status'] == 'failed' ) {
                        $return['status'] = 'failed';
                        $return['message'] = $message['message'];
                    }
                } else {
                    $return['status'] = 'failed';
                    $return['message'] = trans( 'custom.messages.sms-gateway-not-set' );
                }
            } elseif( 'plivo' === $default_sms_gateway ) {
                $auth_id = getSetting( 'auth_id', 'plivo', '');
                $auth_token = getSetting( 'auth_token', 'plivo', '');
                
                if ( ! empty( $auth_id ) && ! empty( $auth_token ) ) {
                    $client = new \Plivo\RestClient($auth_id, $auth_token);

                    $response = $client->accounts->get();
                    
                    if ( $response->cashCredits > 0 ) {
                        $message = $client->messages->create(
                                '919866233855',
                                [$data['tonumber']],
                                $data['content']
                            );
                    } else {
                        $return['status'] = 'failed';
                        $return['message'] = trans( 'custom.messages.sms-gateway-no-credits' );
                    }                    
                } else {
                    $return['status'] = 'failed';
                    $return['message'] = trans( 'custom.messages.sms-gateway-not-set' );
                }
            } // Plivo end.  
            
        } else {
            $return['status'] = 'failed';
            $return['message'] = trans( 'custom.messages.sms-gateway-not-set-sitesettings' );
        }
        return $return;        
    }

    private function errorCodes( $code )
    {
        $mssages = [
            1 => 'Message was not delivered, and no reason could be determined',
            2 => 'Message was not delivered because handset was temporarily unavailable - retry',
            3 => 'The number is no longer active and should be removed from your database',
            4 => 'This is a permanent error:the number should be removed from your database and the user must contact their network operator to remove the bar',
            5 => 'There is an issue relating to portability of the number and you should contact the network operator to resolve it',
            6 => 'The message has been blocked by a carrier\'s anti-spam filter',
            7 => 'The handset was not available at the time the message was sent - retry',
            8 => 'The message failed due to a network error - retry',
            9 => 'The user has specifically requested not to receive messages from a specific service',
            10 => 'There is an error in a message parameter, e.g. wrong encoding flag',
            11 => 'Nexmo cannot find a suitable route to deliver the message - contact support@nexmo.com',
            12 => 'A route to the number cannot be found - confirm the recipient\'s number',

            13 => 'The target cannot receive your message due to their age',
            14 => 'The recipient should ask their carrier to enable SMS on their plan',
            15 => 'The recipient is on a prepaid plan and does not have enough credit to receive your message',
            99 => 'Typically refers to an error in the route - contact support@nexmo.com',
        ];
        return isset( $messages[ $code ] ) ? $messages[ $code ] : '';
    }
    
}
