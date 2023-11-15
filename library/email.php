<?php

/**
 * E-mail Class
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 *
 * @example
 *
 * $m = new Email();
 * $m->setGroup("backup");
 * $m->setSubject("Onderwerp");
 * $m->addBody("First content\r\n");
 * $m->addBody("Second content!");
 * $m->send();
 */

// Require Swiftmailer
require_once "vendors/Swift-5.0.1/swift_required.php";

// E-mail Class
class Email
{
    /**
     * Debug mode
     * @var bool
     */
    private $debug;

    /**
     * Client configuration
     * @var array
     */
    private $c;

    /**
     * E-mail configuration
     * @var array
     */
    private $m;

    /**
     * Plain text body
     * @var string
     */
    private $body;

    /**
     * Receive group
     * @var string
     */
    private $group;

    /**
     * Subject
     * @var string
     */
    private $subject;
    
    /**
     * AttachmentPath
     * @var string
     */
    private $attachmentPath;
    
    /**
     * AttachmentName
     * @var string
     */
    private $attachmentName;
    
    /**
     * Recepients
     * @var array
     */
    private $recepients;

    /**
     * Swiftmailer mailer
     * @var object
     */
    private $SWMail;

    /**
     * Swiftmailer message
     * @var object
     */
    private $SWMsg;

    /**
     * Swiftmailer transport
     * @var object
     */
    private $SWTrans;

    /**
     * Constructor
     *
     * @access public
     * @param  bool $d debug mode
     * @return void
     */
    public function __construct($d = false)
    {
        $this->c = Config::getClientSettings();
        $this->m = Config::getEmailSettings();
        $this->debug = $d;

        if ($this->m["enabled"]) {
            $this->createSWTrans($this->m["transport"]);
            $this->createSWMail();
            $this->createSWMsg();
        }
    }

    /**
     * Create Swift_Mailer
     *
     * @access private
     * @return void
     */
    private function createSWMail()
    {
        $this->SWMail = Swift_Mailer::newInstance($this->SWTrans);

        // Check if tranport is allready started
        if (!$this->SWMail->getTransport()->isStarted()) {
            $this->SWMail->getTransport()->start();
        }
    }

    /**
     * Create Swift_Message
     *
     * @access private
     * @return void
     */
    private function createSWMsg()
    {
        $this->SWMsg = Swift_Message::newInstance();
    }

    /**
     * Create Swiftmailer Transport
     *
     * @access private
     * @param  string $t transport object
     * @return void
     * @throws Exception
     */
    private function createSWTrans($t)
    {
        // SMTP
        if ($t === "smtp") {
            $this->SWTrans = Swift_SmtpTransport::newInstance(
                $this->m["smtp"]["server"],
                $this->m["smtp"]["port"],
                $this->m["smtp"]["security"]
            )
            ->setUsername($this->m["smtp"]["user"])
            ->setPassword($this->m["smtp"]["pass"]);
        }

        // Sendmail
        elseif ($t === "sendmail") {
            if (empty($this->m["sendmail"])) {
                throw new Exception(__METHOD__ . ": " . "Sendmail path is missing!");
            }

            $this->SWTrans = Swift_SendmailTransport::newInstance($this->m["sendmail"] . " -bs");
        }

        // Mail
        elseif ($t === "mail") {
            $this->SWTrans = Swift_MailTransport::newInstance();
        }

        // Error
        else {
            throw new Exception(__METHOD__ . ": Invalid transport!");
        }
    }

    /**
     * Send message
     *
     * @access public
     * @return void
     * @throws Exception
     */
    public function send()
    {
        if ($this->m["enabled"]) {
            try {
                $this->checkProperties();
            } catch (Exception $e) {
                throw new Exception(__METHOD__ . ": " . $e->getMessage());
            }

            $this->addFooter();

            // Debug mode
            if ($this->debug) {
                $l = new Swift_Plugins_Loggers_EchoLogger();
                $this->SWMail->registerPlugin(new Swift_Plugins_LoggerPlugin($l));
            }

            // Set all SWMsg properties
            $this->SWMsg->setBody(self::PRE_HTML . nl2br(trim($this->body)) . self::SUF_HTML, "text/html");
            $this->SWMsg->setFrom(array($this->m["from"] => $this->c["name"]));
            $this->SWMsg->setSubject($this->subject);
            $this->SWMsg->setTo($this->recepients);
            
            if(!empty($this->attachmentPath) && !empty($this->attachmentName)) {
                $this->SWMsg->attach(Swift_Attachment::fromPath($this->attachmentPath)->setFilename($this->attachmentName));
            }
            
            // Send the e-mail
            if (!$this->SWMail->send($this->SWMsg)) {
                if ($this->debug) {
                    $p = $l->dump();
                } else {
                    $p = "An error occured while send e-mail!";
                }
                throw new Exception(__METHOD__ . ": $p");
            }

            // Stop tranport
            $this->SWMail->getTransport()->stop();
        }
    }

    /**
     * Add plain body content
     *
     * @access public
     * @param  string $v body content
     * @return void
     */
    public function addBody($v)
    {
        $this->body .= $v;
    }

    /**
     * Add footer
     *
     * @access public
     * @return void
     */
    public function addFooter()
    {
        $this->addbody("\r\n\r\n<div class=\"footer\">Dit is een geautomatiseerd bericht vanuit het TechniX Garment System\r\n\r\nDeze e-mail is uitsluitend bestemd voor de geadresseerde(n). Verstrekking aan en gebruik door anderen is niet toegestaan.\r\nTechnico Automatisering B.V. sluit iedere aansprakelijkheid uit die voortvloeit uit deze elektronische verzending.</div>");
    }

    /**
     * Add title
     *
     * @access public
     * @param  string $v title
     * @return void
     */
    public function addTitle($v)
    {
        $this->body .= "\r\n<h1>$v</h1>";
    }

    /**
     * Set group
     *
     * @access public
     * @param  string $v group name
     * @return void
     * @throws Exception
     */
    public function setGroup($v)
    {
        $this->group = strtoupper(trim($v));

        if (!isset($GLOBALS["_EMAIL"]["GROUP"][$this->group])) {
            throw new Exception(__METHOD__ . ": '" . $this->group . "' is not a valid group!");
        }

        $this->setRecepients($GLOBALS["_EMAIL"]["GROUP"][$this->group]);
    }

    /**
     * Set subject
     *
     * @access public
     * @param  string $v subject of e-mail
     * @return void
     */
    public function setSubject($v)
    {
        $this->subject = ucfirst(trim($v));
    }
    
    /**
     * Set attachment
     *
     * @access public
     * @param  string $v attachment of e-mail
     * @return void
     */
    public function setAttachment($v, $w)
    {
        $this->attachmentPath = trim($v);
        $this->attachmentName = trim($w);
    }

    /**
     * Set recepients
     *
     * @access public
     * @param  array $v send e-mail to these recepients
     * @return void
     */
    public function setRecepients($v)
    {
        $this->recepients = $v;
    }

    /**
     * Check properties
     *
     * @access private
     * @return void
     * @throws Exception
     */
    private function checkProperties()
    {
        // Check body
        if (empty($this->body)) {
            throw new Exception(__METHOD__ . ": Body e-mail is empty!");
        }

        // Check subject
        elseif (empty($this->subject)) {
            throw new Exception(__METHOD__ . ": Subject e-mail is empty!");
        }

        // Check fromAddress
        elseif (empty($this->m["from"])) {
            throw new Exception(__METHOD__ . ": Sender e-mail is empty!");
        }

        // Check To
        elseif ((empty($this->recepients)) || (!is_array($this->recepients))) {
           throw new Exception(__METHOD__ . ": Receiver of message is empty of not an array!");
        }
    }

    const SUF_HTML = "</body></html>";
    const PRE_HTML = "<html><head><style>
        html,body{ background-color:#fbfcfc; color:#525252; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:0.9em; }
        body{ line-height:1.3em; }
        h1{ border-top:1px solid #c0c0c0; color:#1c5a39; font-size:1.1em; margin:0; padding:20px 0 5px 0; }
        .footer{ border-top:1px solid #c0c0c0; color:#c0c0c0; font-size:0.9em; padding:10px 0 0 0; }
        .green{ color: #1c5a39; }
        .red{ color: #ff0000; }
    </style></head><body>";
}

?>
