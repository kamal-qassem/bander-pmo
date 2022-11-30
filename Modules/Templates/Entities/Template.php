<?php

namespace Modules\Templates\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'key', 'type', 'subject', 'from_email', 'from_name', 'content', 'status'];
    protected $hidden = [];
    public static $searchable = [
    ];
    
    public static function boot()
    {
        parent::boot();

        Template::observe(new \App\Observers\UserActionsObserver);
    }

    public static $enum_type = ["Content" => "Content", "Header" => "Header", "Footer" => "Footer"];

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCreatedByIdAttribute($input)
    {
        $this->attributes['created_by_id'] = $input ? $input : null;
    }
    
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

     /**
     * [getRecordWithSlug description]
     * @param  [string] $slug [description]
     * @return [array]       [description]
     */
    public static function getRecordWithSlug($slug)
    {
        return Template::where('key','=',$slug)->first();
    }
    

     /**
     * Common email function to send emails
     * @param  [type] $template [key of the template]
     * @param  [type] $data     [data to be passed to view]
     * @return [type]           [description]
     */
    public function sendEmail($template, $data)
    {   
        $template = Template::where('key', '=', $template)->first();
    
        $content = \Blade::compileString($this->getTemplate($template, $data));
        $result = $this->render($content, $data);
        

        \Mail::send('admin.invoices.mail.template', ['body' => $result], function ($message) use ($template, $data) 
        {
            

            $from_email = getSetting('contact_email','site_settings');
            $from_name = getSetting('site_title','site_settings');
            if ( ! empty( $template->from_email ) ) {
                $from_email = $template->from_email;
            }
            if ( ! empty( $template->from_name ) ) {
                $from_name = $template->from_name;
            }

            $message->from($from_email, $from_name);
            $message->to($data['to_email']);
            if ( ! empty( $data['ccemail'] )) {
               
                $ccemail = explode(',', $data['ccemail']);
                $message->cc( $ccemail );
            }
            
            if ( ! empty( $data['bccemail'] )) {      
                
                $bccemail = explode(',', $data['bccemail']);
                $message->cc( $bccemail );
            }
            if ( ! empty( $data['bccemail_admin'] )) {
               
                $bccemail_admin = explode(',', $data['bccemail_admin']);
                $message->cc( $bccemail_admin );
            }
            
            if ( ! empty( $data['attachments'] )) {
                foreach($data['attachments'] as $file) {
                    $message->attach($file);
                }
            }
            $message->subject($template->subject);
        });
    }

    /**
     * Returns the template html code by forming header, body and footer
     * @param  [type] $template [description]
     * @return [type]           [description]
     */
    public function getTemplate($template, $data)
    {
        
        $header = Template::where('title', '=', 'header')->first();

        $footer = Template::where('title', '=', 'footer')->first();

        $content = $template->content;
        if ( isset( $data['content'] ) ) {
            $content = $data['content'];
        }
           
        $view = \View::make('admin.invoices.mail.template', [
                                                'header' => $header->content, 
                                                'footer' => $footer->content,
                                                'body'  => $content, 
                                                ]);

        return $view->render();
    }

    /**
     * Prepares the view from string passed along with data
     * @param  [type] $__php  [description]
     * @param  [type] $__data [description]
     * @return [type]         [description]
     */
    public function render($__php, $__data)
    {
        $obLevel = ob_get_level();
        ob_start();
        extract($__data, EXTR_SKIP);
        try {
            eval('?' . '>' . $__php);
        } catch (Exception $e) {
            while (ob_get_level() > $obLevel) ob_end_clean();
            throw $e;
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) ob_end_clean();
            throw new FatalThrowableError($e);
        }
        return ob_get_clean();
    }
}
