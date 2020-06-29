<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * recordInstructorsRests.php
 *
 * Record instructor rest add/modify/cancel
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
 * @category   booking management
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: recordInstructorsRests.php,v 1.5.2.3 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon Feb 24 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

require_once('./includes/redirect.php');
require_once('./displayClasses/requestForm.class.php');

// Variables that should be posted :
// $exceptionnalSlot
// $instructor
// $start_year,month,day,hour,minute and the same for $end
// $start_day,time and the same for $end

////////////////// local functions managing displays /////////////////////////////////

function displayBadRequest($title)
{
    global $lang;
    global $instructor;
    global $sub_menu;
    global $old_start_date;
    global $old_end_date;
    global $old_start_time;
    global $old_end_time;
    global $outdated_flag;

    require_once('./includes/header.php');
    ?></head><body onload="document.getElementById('validation').focus();"><?php

    $mainMes='';
    switch($outdated_flag)
    {
        case 1:
            $mainMes=$lang['REST_OUT_SUBSCRIPTION'];
            break;
        case 2:
            $mainMes=$lang['REST_OWN_OUT_SUBCRIPTION'];
            break;
    }
    $request=new requestForm($mainMes);
    $request->addHidden('menu',4);
    $request->addHidden('sub_menu',$sub_menu-10);
    $request->addHidden('old_instructor',$instructor);
    $request->addHidden('ts_old_start_date',$old_start_date->getTS());
    $request->addHidden('ts_old_end_date',$old_end_date->getTS());
    $request->addHidden('ts_old_start_time',$old_start_time->getNNSV());
    $request->addHidden('ts_old_end_time',$old_end_time->getNNSV());
    $request->addTitle($title);
    $request->close($lang['BACK_BUTTON']);
    require_once('./includes/footer.php');
}

////////////////////////////// Main code start here /////////////////////////////////
if($database->query('lock tables instructors read, profiles read, authentication read, exceptionnal_inst_dates write, regular_presence_inst_dates write'))
{
    $outdated_flag=0;
    $permits=$userSession->getPermits();

    // flag used along the script to end it in case of wrong parameters
    $ok_flag=true;

    define_global('exceptionnalSlot',0);
    define_global('ts_old_start_time','0,00:00:00');
    $old_start_time=new ofDateSpan($ts_old_start_time.':00');
    define_global('ts_old_end_time','0,00:00:00');
    $old_end_time=new ofDateSpan($ts_old_end_time.':00');
    define_global('old_instructor',0);
    define_global('instructor',0);
    define_global('old_presence',0);
    define_global('presence',0);
    define_global('ts_old_end_date','');
    $old_end_date=new ofDate($ts_old_end_date);
    // $ts_old_start_date is define in index.php
    $old_start_date=new ofDate($ts_old_start_date);
    define_global('start_year');
    define_global('start_month');
    define_global('start_day');
    define_global('start_hour');
    define_global('start_minute');
    define_global('end_year');
    define_global('end_month');
    define_global('end_day');
    define_global('end_hour');
    define_global('end_minute');
    $start_date=new ofDate($start_year.$start_month.$start_day.$start_hour.$start_minute.'00');
    $end_date=new ofDate($end_year.$end_month.$end_day.$end_hour.$end_minute.'00');
    $start_date->setTZ($userSession->getTimeZone());
    $end_date->setTZ($userSession->getTimeZone());
    $start_date->convertTZbyID('UTC');
    $end_date->convertTZbyID('UTC');
    define_global('regular_start_day',0);
    define_global('regular_start_hour','00');
    define_global('regular_start_minute','00');
    define_global('regular_end_day',0);
    define_global('regular_end_hour','00');
    define_global('regular_end_minute','00');
    $start_time=new ofDateSpan($regular_start_day.','.$regular_start_hour.':'.$regular_start_minute.':00');
    $end_time=new ofDateSpan($regular_end_day.','.$regular_end_hour.':'.$regular_end_minute.':00');
    if($ok_flag)
    {
        // we have to check if member is allowed to manage instructors rest
        if(!$userSession->isInstructor()AND!$userSession->isFreezeInstructorAllowed())
        {
            // member is not allowed
            displayBadRequest($lang['REST_MANAGE_NOT_ALLOWED']);
            $ok_flag=false;
        }
    }
    if($ok_flag)
    {
        if($sub_menu==12)
        {
            ///////////// IT'S A REAL BOOK
            if($exceptionnalSlot)
            {
                $query='insert into exceptionnal_inst_dates (INST_NUM,START_DATE,END_DATE,PRESENCE) values (\''.$instructor.'\',\''.$start_date->getDate().'\',\''.$end_date->getDate().'\',\''.$presence.'\')';
            }
            else
            {
                $query='insert into regular_presence_inst_dates (INST_NUM,START_DAY,END_DAY,START_HOUR,END_HOUR) values (\''.$instructor.'\',\''.$start_time->day.'\',\''.$end_time->day.'\',\''.$start_time->getClock().'\',\''.$end_time->getClock().'\')';
            }
        }
        elseif($sub_menu==14)
        {
            // IT'S A MODIFICATION
            if($exceptionnalSlot)
            {
                $query='update exceptionnal_inst_dates set INST_NUM=\''.$instructor.'\', START_DATE=\''.$start_date->getDate().'\', END_DATE=\''.$end_date->getDate().'\', PRESENCE=\''.$presence.'\' 
                where INST_NUM=\''.$old_instructor.'\' and START_DATE=\''.$old_start_date->getDate().'\' and END_DATE=\''.$old_end_date->getDate().'\' and PRESENCE=\''.$old_presence.'\'';
            }
            else
            {
                $query='update regular_presence_inst_dates set INST_NUM=\''.$instructor.'\', START_DAY=\''.$start_time->day.'\', END_DAY=\''.$end_time->day.'\', START_HOUR=\''.$start_time->getClock().'\', END_HOUR=\''.$end_time->getClock().'\' 
                where INST_NUM=\''.$old_instructor.'\' and START_DAY=\''.$old_start_time->day.'\' and END_DAY=\''.$old_end_time->day.'\' and START_HOUR=\''.$old_start_time->getClock().'\' and END_HOUR=\''.$old_end_time->getClock().'\'';
            }
        }
        elseif($sub_menu==13)
        {
            // IT'S A CANCELLATION
            if($exceptionnalSlot)
            {
                $query='delete from exceptionnal_inst_dates where INST_NUM=\''.$old_instructor.'\' and START_DATE=\''.$old_start_date->getDate().'\' and END_DATE=\''.$old_end_date->getDate().'\' and PRESENCE=\''.$old_presence.'\'';
            }
            else
            {
                $query='delete from regular_presence_inst_dates where INST_NUM=\''.$old_instructor.'\' and START_DAY=\''.$old_start_time->day.'\' and END_DAY=\''.$old_end_time->day.'\' and START_HOUR=\''.$old_start_time->getClock().'\' and END_HOUR=\''.$old_end_time->getClock().'\'';
            }
        }
        if($database->query($query))
        {
            redirect($userSession,$start_date->getTS());
        }
        else
        {
            displayBadRequest($lang['ERROR_TRANSMIT_DATA']);
        }
    }
    $database->query('unlock tables');
}
?>