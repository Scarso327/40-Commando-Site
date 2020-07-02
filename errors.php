<?php
/**
 * Created by PhpStorm.
 * User: ScarsoLP
 * Date: 15/03/2018
* Time: 10:41 PM
*/
return array(
    // Your errors (Any custom errors you wish to create make below here using the template below)
    // The ID must ALWAYS begin with a hastag as it's needed for custom links etc.
    // '#ID' => array('TITLE','DESCRIPTION'),
    // Fe Errors (Custom Framework Errors)
    '#Fe001' => array('BAD REDIRECT','We don\'t have such a redirect outlined in our system! <br>Please return <a href="'.URL.'">home!</a>'),
    '#Fe002' => array('BAD Controller','The Controller you\'re trying to access exists in both the Application and a plugin! <br>Please return <a href="'.URL.'">home!</a>'),
    '#Fe003' => array('UNKNOWN ERROR','An error has occurred that the system is unable to fix. <br>Please return <a href="'.URL.'">home!</a>'),
    '#Fe004' => array('DATABASE FAILURE','An error has occurred when trying to preform an action using the database. <br>Please return <a href="'.URL.'">home!</a>'),
    '#Fe005' => array('MEMBER NOT FOUND','This member doesn\'t exist. <br>Please return <a href="'.URL.'">home!</a>'),
    '#Fe006' => array('FIELDS MISSING','An error seems to have occured with the creation of this form...'),
    '#Fe007' => array('FIELDS EMPTY','A required field has no data...'),
    '#Fe008' => array('INVALID STEAMID','A steamid was entered that failed the regex test...'),
    '#Fe009' => array('NAME ALREADY TAKEN','This name is already taken by someone within this factions history...'),
    '#Fe010' => array('REQUIRES PREDEFINED STEAMID','This form requires a valid predefined steamid...'),
    '#Fe011' => array('INSUFFICENT PERMISSIONS','You don\'t have the required permissions to perform this action...'),
    '#Fe012' => array('RECRUITMENT COMPLETED','You\'re already a member of 40 Commando, A Coy...'),
    '#Fe013' => array('NAME TAKEN','The name you\'ve entered is already taken...'),
    '#Fe014' => array('ALREADY HAS AWARD','This member already has this award...'),
    '#Fe015' => array('PRIMARY CAN\'T ALSO BE SECONDARY','You can\'t have the member\'s primary assignment in their secondary assignments...'),
    // Normal Errors
    '#400' => array('BAD REQUEST','We have received a request that does not quite look right! <br>Please refresh your browser or return <a href="'.URL.'">home!</a>'),
    '#401' => array('AUTHENTICATION REQUIRED','We require authentication to be allow to preform this task! <br>Please return <a href="'.URL.'">home!</a>'),
    '#403' => array('ACCESS FORBIDDEN','You don\'t have the required access to access this page or preform that task! <br>Please return <a href="'.URL.'">home!</a>'),
    '#404' => array('PAGE NOT FOUND','The page you have requested cannot be found on our site! <br>If you believe this to be an error please report it to the web master. <br>If you believe it is not an error, return <a href="'.URL.'">home!</a>'),
    '#500' => array('INTERNAL SERVER ERROR', 'Oh no! We have experienced an error when processing your request! <br>If you are able to please report it to the web master. <br><a href="'.URL.'">Safety</a>')
);