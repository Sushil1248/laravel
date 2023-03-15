<?php
 
namespace App\Traits;

use Illuminate\Support\Facades\Mail;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

use App\Models\AutoResponder;
use App\Models\SmtpInformation;
use App\Models\EmailLog;
use Spatie\Permission\Models\Permission;
use Crypt;
 
trait AutoResponderTrait {
 
    public function get_template_by_name($name) {
 		$template = AutoResponder::where('template_name', $name)->first(['id', 'template_name','subject','template']);
 		return $template;
    }

    public function get_smtp_info() {
 		$smtp = SmtpInformation::where('status', 1)->first(['host', 'port', 'from_email', 'username', 'from_name', 'password', 'encryption']);
 		return $smtp;
    }
    public function get_permisions_by_group($name) {
      
        $permissions = Permission::where('group_name', $name)->get(['id', 'name']);
 
        return $permissions;
    }
    
    public function email_log_create($to, $template_id, $template_name) {
        $data = [
            'to_email' => $to,
            'auto_responder_id' => $template_id,
            'template_name' => $template_name
        ];
        $record = EmailLog::create($data);
        return $record->id;
   }
   public function email_log_update($id) {
       $data = ['status' => 1 ];
       $record = EmailLog::where('id', $id)->update($data);
  }

    public function send_mail($to, $subject, $email_body, $cc = NULL){
        
        $smtp = $this->get_smtp_info();
        // $password = Crypt::decrypt($smtp->password);
        $password = $smtp->password;
        // Create the Transport
        $transport = (new \Swift_SmtpTransport($smtp->host, $smtp->port, $smtp->encryption ))
        ->setUsername($smtp->username)
        ->setPassword($password);
        
        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);
        
        // Create a message
        $message = (new Swift_Message($subject))
        ->setFrom([$smtp->from_email => $smtp->from_name])
        ->setTo($to);
        if($cc)
        $message->setCc($cc);
        $message->setBody($email_body, 'text/html');

        // Send the message
        $result = $mailer->send($message);
        return $result;

    }
 
}