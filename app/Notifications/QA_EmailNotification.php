<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Templates\Entities\Template;

class QA_EmailNotification extends Notification
{
    use Queueable;

    public $email;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForMail($notification)
    {
        if ( ! empty( $this->data['email'] ) ) {
            return $this->data['email'];
        } else {
            return $this->email;
        }
    }

    public function toMail($notifiable)
    {
        $template = $this->data["template"];
        $template = Template::where('key', '=', $template)->first();

        $data = $this->data['data'];
        $content = \Blade::compileString($this->getTemplate($template, $this->data));
        $message = $this->render($content, $data);

        // dd( $message );
        // die( $this->email );

        $mailmessage = (new MailMessage)
        ->subject( $template->subject )
        ->markdown('admin.invoices.mail.template', ['body' => $message]);

        if ( ! empty( $this->data['attachment'] ) ) {
            $mailmessage->attach( $this->data['attachment'] );
        }

        return $mailmessage;
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

}
