<?
/*
 * Confirmation from the constituent that they want to send the
 * fax/email.  This page is linked to from the email which confirms the
 * constituent's email address.
 * 
 * Copyright (c) 2004 UK Citizens Online Democracy. All rights reserved.
 * Email: francis@mysociety.org. WWW: http://www.mysociety.org
 *
 * $Id: confirm.php,v 1.17 2008-03-10 17:49:35 matthew Exp $
 * 
 */

require_once "../phplib/fyr.php";
require_once "../phplib/queue.php";

require_once "../../phplib/utility.php";

fyr_rate_limit(array());

$ad = get_http_var('ad');
if ($ad) {
    $values = array(
        'recipient_via' => null, 'recipient_name' => 'Recipient Name', 'recipient_type' => 'Type',
        'sender_name' => 'Sender Name', 'sender_email' => 'email', 'sender_postcode' => 'SW1A1AA',
        'advert' => $ad,
    );
    template_draw("confirm-accept", $values);
    exit;
}

$token = get_http_var('token');
if (!$token) {
    template_show_error("Please make sure you copy the URL from your
        email properly. The token was missing.");
}

$result = msg_confirm_email($token);
if (rabx_is_error($result)) {
    template_show_error($result->text);
}
if (!$result) {
    template_draw("confirm-trouble");
} else {
    $values = msg_admin_get_message($result);
    if (rabx_is_error($values)) {
        template_show_error($values->text);
    } elseif ($values['cobrand'] && $values['cobrand'] == 'animalaid') {
        header("Location: http://www.animalaiduk.com/h/f/ACTIVE/blog//1//?id=".$values['cocode']);
        exit;
    } else {
        template_draw("confirm-accept", $values);
    }
}

?>

