<?php
/**
* DONOT modify this file as it's automatically generated based on setting parameters.
**/
class MessagingSettings{
	//
	const CONFIRM_EMAIL                                   = '<p>You have made a posting to {SITE_NAME}. Thanks!<br /><br />Your posting titled \"{TITLE_OF_POST}\" needs an activation to be live on the site.<br />To confirm and publish your ad please click the following link (or copy and paste it into your browser)<br /><a href=\"{ACTIVATELINK}\">{ACTIVATELINK}</a> <br /><br />To amend or delete your posting please click the following link:<br /><a href=\"{MODIFIEDLINK}\">{MODIFIEDLINK}</a>. <br /><br />Please note: We reserve the RIGHT TO refuse OR DELETE postings that we believe are inappropriate or which breach our terms and conditions.<br /><br /><a href=\"{SITE_URL}\">{SITE_NAME}</a></p>';
	//
	const EMAIL_TEMPLATE_FOLDER                           = 'protected/runtime/emails';
	//
	const EXPIRATION_EMAIL                                = 'Hi,your ad {AD_TITLE} on {URL_OF_THE_SITE} has expired.Regards,{NAME_OF_THE_SITE}';
	//
	const REGISTRATION_EMAIL                              = 'Hi {USER_EMAIL_ADDRESS},<br /><br /><p>Thank you for registering on {NAME_OF_THE_SITE}. You can <a href=\"{URL_OF_SIGN_IN_PAGE}\">sign in</a> on {SITE_URL} using your email address and the password : {PASSWORD}<br /><br />It is recommended that you <strong>change your password</strong> at your first login.<br /><br />Thanks,<br />{NAME_OF_THE_SITE}';
	//
	const REPUBLISH_EMAIL                                 = '<p>You have made a posting to {SITE_NAME}. Thanks!<br /><br />Title of your ad : <strong>{TITLE_OF_POST}</strong>. Your posting will remain for {REMAINING_DAYS} days. <br /><br />To get more responses, you can place your ad at the top of listings (\"Top ads\" section) for {TIME1} or {TIME2} days. <br />Please follow this link (or copy the link in your browser): <br /><a href=\"../p,FEATURELINK\">http://www.titanclassified.com/classified/p,FEATURELINK</a> <br /><br />To republish your posting please follow this link (or copy the link in your browser) and click \"republish\": <br /><a href=\"../p,REPUBLISHLINK\">http://titan.classifieds.com/classified/p,REPUBLISHLINK</a> <br /><br />To amend or delete your posting please follow this link (or copy the link in your browser): <br /><a href=\"../p,MODIFIEDLINK\">http://titan.classifieds.com/classified/p,MODIFIEDLINK</a> <br /><br />If you have any questions or problems with your posting you can find the help you need in \"{SITE_NAME} Help\". Please note: We reserve the right to refuse or delete postings that we believe are inappropriate or which breach our terms and conditions.<br /><br /><a href=\"http://titan.classifieds.com/classified\">http://titan.classifieds.com/classified</a></p>';
	//
	const RESET_PASSWORD_EMAIL                            = 'Hi {USER_EMAIL_ADDRESS},<br /><br /><p>Your password was reinitialized on {NAME_OF_THE_SITE}.<br /><br />New password: {PASSWORD}<br /><br />You can now login on {SITE_URL} using this link: {URL_OF_SIGN_IN_PAGE}<br /><br />Thanks,<br />{NAME_OF_THE_SITE}';

}
?>