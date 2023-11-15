<?php

/**
 * Backup functions
 *
 * These functions are used in the backup script ../backup.php
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2012 Technico Automatisering B.V. All rights reserved.
 * @version   1.0
 */

/**
 * Generate an error
 *
 * @param string $message Error message
 * @param boolean $quit If true, the script will die
 *
 * @return void
 */
function genError($message, $quit = true)
{
    /** Send an e-mail with error message */
    sendEmail ('Foutmelding: ' . $message, 'Backup mislukt');

    if ($quit)
    {
        die ($message);
    }
    else
    {
        echo $message;
    }
}

/**
 * Send an e-mail
 *
 * @param string $text Text in e-mail
 * @param string $subject Subject of e-mail
 *
 * @return void
 */
function sendEmail($text, $subject)
{
    /** Define the transport **/
    $transport = Swift_SmtpTransport::newInstance('mfp01.technico.nl', 25);

    /** Create the Mailer using created Transport */
    $mailer = Swift_Mailer::newInstance($transport);

    /** Create the email instance */
    $email = Swift_Message::newInstance()

    /** Set the charset */
    ->setCharset('UTF-8')

    /** Give the email a subject */
    ->setSubject($subject)

    /** Set the From address with an associative array */
    ->setFrom(array('backup@technico.nl' => 'Database Backup'))

    /** Set the To address with an associative array */
    ->setTo(array('edwin@technico.nl' => 'Edwin van de Pol'))

    /** Give it a body */
    ->setBody('<html>
            <head>
                <style type="text/css" media="all">
                    body{ color:#000; font-family:Verdana, Arial; font-size:14px; }
                    span.green{ color:#00baa5; }
                </style>
            </head>
            <body>
                ' . $text . '<br /><br />--<br /><strong>Techni<span class="green">X</span> GS - Database Backup v' . VERSION . '</strong>
            </body>
        </html>', 'text/html')

    /** And optionally an alternative body */
    ->addPart($text . "\n\n--\nTechniX GS - Database Backup v" . VERSION, 'text/plain');

    /** Send the email */
    $result = $mailer->send($email);
}

/**
 * Validates backup configuration variables
 *
 * @param array $config Array with config variables
 *
 * @return void
 */
function validateBackupConfig($config)
{
    /** Protocol */
    if ((!is_string($config['protocol'])) || (empty($config['protocol'])) ||
                  (($config['protocol'] !== 'smb') &&
                   ($config['protocol'] !== 'ftp') &&
                   ($config['protocol'] !== 'sftp')))
    {
        genError ('Invalid or missing value for \'protocol\'. Must be string. Check config file.');
    }

    /** Server */
    if ((!is_string($config['server'])) || (empty($config['server'])))
    {
        genError ('Invalid or missing value for \'server\'. Must be string. Check config file.');
    }

    /** Username */
    if ((!is_string($config['username'])) && (!is_null($config['username'])))
    {
        genError ('Invalid or missing value for \'username\'. Must be string or null. Check config file.');
    }

    /** Password */
    if ((!is_string($config['password'])) && (!is_null($config['password'])))
    {
        genError ('Invalid or missing value for \'password\'. Must be string or null. Check config file.');
    }

    /** Path */
    if ((!is_string($config['path'])) && (!is_null($config['path'])))
    {
        genError ('Invalid or missing value for \'path\'. Must be string or null. Check config file.');
    }
    elseif (substr($config['path'], -1, 1) !== '/')
    {
        genError ('Invalid value for \'path\'. Append trailing slash. Check config file.');
    }
}

?>
