<?php

/**
 * Used to ease the sending of emails
 */
class MailHelper{

    public static function send($to, $email_from, $email_from_name, $plain_message, $html_message, $email_subject_prefix, $subject)
    {

        //auto-load the bundle
        Bundle::start('swiftmailer');

        // Get the Swift Mailer instance
       $mailer = IoC::resolve('mailer');


        // Create the Transport
        // $transport = Swift_SmtpTransport::newInstance('smtp.postmarkapp.com', 2525)
        //     ->setUsername('0ff1463b-4b4d-493a-a3f4-82352e80e342')
        //     ->setPassword('0ff1463b-4b4d-493a-a3f4-82352e80e342')
        // ;

   
        // $mailer = Swift_Mailer::newInstance($transport);


        // Construct the message
        $message = Swift_Message::newInstance($email_subject_prefix.': '.$subject)
                                ->setFrom(array( 'no-reply@portal.com' => 'Portal' ))
                                ->setTo($to)
                                ->setBcc('demo@portal.com')
                                ->addPart($plain_message,'text/plain')
                                ->setBody($html_message, 'text/html');


        // Send the email
      return $mailer->send($message);

    }//send


}//end class