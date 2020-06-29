<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * mail.class.php
 *
 * send mail
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program (file license.txt);
 * if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * @category   mail
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: mail.class.php,v 1.10.2.6 2006/06/19 07:12:31 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon Mar 24 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

// Pear Date class
require_once('Mail.php');

/*
* ofMail class
*
* call the PEAR::Mail class
* specific functions for OF
*/
class ofMail
{
    var $mail;      // mail class created by the factory() Mail function
    var $headers;   // headers used for every mail
    var $recipients;
    var $copyRecipients;

    /**
     * Constructor
     *
     * Creates a new ofMail Object initialized with the driver setted in config.php
     *
     * @access public
     * @param null
     * @return null
     */
    function ofMail($fromAddress)
    {
        $this->headers['From']=$fromAddress;

        $params=array();

        switch (MAIL_FACTORY)
        {
        case 'mail':
            ini_set('SMTP',MAIL_HOST);
            break;
        case 'smtp':
            $params['host']=MAIL_HOST;
            $params['port']='25';
//            $params['debug']=true;        // useful to debug
            if ((MAIL_AUTH_NAME!='')and(MAIL_AUTH_PASSWORD!=''))     // in case of smtp authentication
            {
                $params['username']=MAIL_AUTH_NAME;
                $params['password']=MAIL_AUTH_PASSWORD;
            }
            break;
        default:
            break;
        }
        $this->mail=&Mail::factory(MAIL_FACTORY,$params);

        $this->clearRecipients();
        $this->clearCopyRecipients();
    }

    /**
     * Reset recipients list
     *
     * @access public
     * @param null
     * @return null
     */
    function clearRecipients()
    {
        $this->recipients=array();
    }

    /**
     * Reset copy recipients list
     *
     * @access public
     * @param null
     * @return null
     */
    function clearCopyRecipients()
    {
        $this->copyRecipients=array();
    }

    /**
     * add one recipient to the list
     *
     * @access public
     * @param string $recipient
     * @return null
     */
    function addRecipient($recipient)
    {
        if (!in_array($recipient,$this->recipients))
        {
            $this->recipients[sizeof($this->recipients)]=$recipient;
        }
    }

    /**
     * add one copy recipient to the list
     *
     * @access public
     * @param string $recipient
     * @return null
     */
    function addCopyRecipient($recipient)
    {
        if (!in_array($recipient,$this->copyRecipients)) {
            $this->copyRecipients[]=$recipient;
        }
    }
    
    /**
     * Implements Mail::send() function using php's built-in mail()
     * command.
     *
     * @param mixed $recipients Either a comma-seperated list of recipients
     *              (RFC822 compliant), or an array of recipients,
     *              each RFC822 valid. This may contain recipients not
     *              specified in the headers, for Bcc:, resending
     *              messages, etc.
     *
     * @param string $subject The subject in the header
     *
     * @param string $body The full text of the message body, including any
     *               Mime parts, etc.
     *
     * @return mixed Returns true on success, or a PEAR_Error
     *               containing a descriptive error message on
     *               failure.
     * @access public
     */
    function send($subject,$body)
    {
        $now = new ofDate();
        $this->headers['To']=$this->recipients;
        $this->headers['CC']=$this->copyRecipients;
        $this->headers['Subject']=$subject;
        $this->headers['Date']=$now->format('%a, %e %b %Y %T -0000');
        $allRecipients = array_merge($this->recipients, $this->copyRecipients);
        if (sizeof($allRecipients) > 0) {
			return $this->mail->send($allRecipients, $this->headers, $body);
        }
        else {
            return true;
        }
    }
}
?>