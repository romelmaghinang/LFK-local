<?php 
$checkedHTML = '<img src="https://lote4kids.com/wp-content/themes/lote4kids-child/assets/img/email-check-final4.png" width="14" height="14" style="vertical-align:middle;" alt="Done">';
$uncheckedHTML = '<img src="https://lote4kids.com/wp-content/themes/lote4kids-child/assets/img/email-uncheck-final4.png" width="14" height="14" style="vertical-align:middle;" alt="">';
?>

<html><body>

    <p>Hi <?php echo esc_html($first_name); ?>,</p>
    <p>Your <i>LOTE4Kids</i> trial has now wrapped up. Here is a summary of your activity for access code <?php echo esc_html($barcodes_str); ?>:</p>
    <ul>
        <li>Languages explored: <?php echo esc_html(!empty($language_viewed_15days) ? $language_viewed_15days : 'None'); ?></li>
        <li>Books read: <?php echo esc_html($fifteen_days_results['number_of_views_read'] ?? '0');  ?></li>
        <li>Quizzes started: <?php echo esc_html($fifteen_days_results['number_of_quizzes_started']) ?? '0';  ?></li>
        <li>Activities completed: <?php echo esc_html($fifteen_days_results['number_of_activities']) ?? '0';  ?></li>
        <li>Total engagements: <?php echo esc_html($fifteen_days_results['total_engagement']) ?? '0'; ?></li>
    </ul>

    <p>Here's how you tracked against the trial checklist:</p>
    <p>
		<table width="400" cellpadding="0" cellspacing="0" border="0" style="font-family: Arial, sans-serif; font-size: 14px; border-collapse: collapse; border: 1px solid #cccccc;">
			<thead>
				<tr>
					<td style="background-color: #b8e3dc; color: #222222; text-align: center; padding: 6px 14px; font-size: 14px; font-weight: bold;">Trial Progression</td>
				</tr>
			</thead>
			<tbody>
				<tr style="background-color: #ffffff;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;"><?php echo !empty($checklistArr['flipbook']) ? $checkedHTML : $uncheckedHTML; ?>&nbsp; 1. <a href="<?php echo $checklist_flipbook__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_flipbook__text; ?></a></td>
				</tr>
				<tr style="background-color: #efefef;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;"><?php echo !empty($checklistArr['quiz']) ? $checkedHTML : $uncheckedHTML; ?>&nbsp; 2. <a href="<?php echo $checklist_quiz__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_quiz__text; ?></a></td>
				</tr>
				<tr style="background-color: #ffffff;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;"><?php echo !empty($checklistArr['video']) ? $checkedHTML : $uncheckedHTML; ?>&nbsp; 3. <a href="<?php echo $checklist_video__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_video__text; ?></a></td>
				</tr>
				<tr style="background-color: #efefef;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;"><?php echo !empty($checklistArr['picture_card']) ? $checkedHTML : $uncheckedHTML; ?>&nbsp; 4. <a href="<?php echo $checklist_picture_card__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_picture_card__text; ?></a></td>
				</tr>
				<tr style="background-color: #ffffff;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;"><?php echo !empty($checklistArr['non_fiction']) ? $checkedHTML : $uncheckedHTML; ?>&nbsp; 5. <a href="<?php echo $checklist_non_fiction__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_non_fiction__text; ?></a></td>
				</tr>
				<tr style="background-color: #efefef;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;"><?php echo !empty($checklistArr['sign_language']) ? $checkedHTML : $uncheckedHTML; ?>&nbsp; 6. <a href="<?php echo $checklist_sign_language__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_sign_language__text; ?></a></td>
				</tr>
				<tr style="background-color: #ffffff;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;"><?php echo !empty($checklistArr['fun_facts']) ? $checkedHTML : $uncheckedHTML; ?>&nbsp; 7. <a href="<?php echo $checklist_fun_facts__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_fun_facts__text; ?></a></td>
				</tr>
				<tr style="background-color: #efefef;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;"><?php echo !empty($checklistArr['lekti']) ? $checkedHTML : $uncheckedHTML; ?>&nbsp; 8. <a href="<?php echo $checklist_lekti__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_lekti__text; ?></a></td>
				</tr>
				<tr style="background-color: #ffffff;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;"><?php echo !empty($checklistArr['activities']) ? $checkedHTML : $uncheckedHTML; ?>&nbsp; 9. <a href="<?php echo $checklist_activities__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_activities__text; ?></a></td>
				</tr>
				<tr style="background-color: #efefef;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;"><?php echo !empty($checklistArr['mobile']) ? $checkedHTML : $uncheckedHTML; ?>&nbsp; 10. <a href="<?php echo $checklist_mobile__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_mobile__text; ?></a></td>
				</tr>
				<tr style="background-color: #ffffff;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;"><?php echo !empty($checklistArr['overview_video']) ? $checkedHTML : $uncheckedHTML; ?>&nbsp; 11. <a href="<?php echo $checklist_overview_video__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_overview_video__text; ?></a></td>
				</tr>
				<tr style="background-color: #efefef;">
					<td style="padding: 6px 14px;"><?php echo !empty($checklistArr['staff_portal']) ? $checkedHTML : $uncheckedHTML; ?>&nbsp; 12. <a href="<?php echo $checklist_staff_portal__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_staff_portal__text; ?></a></td>
				</tr>
			</tbody>
		</table>
    </p>

    <p>It looks like there may not have been enough time to fully explore everything <i>LOTE4Kids</i> has to offer. If that's the case, we'd be happy to arrange a complimentary extension so you and your team can take a proper look.</p>
    
    <?php    
    $parsedUrl = parse_url($trialExtensionLink);
    $pathParts = explode('/', rtrim($parsedUrl['path'], '/'));
    array_pop($pathParts); 
    $trialPath = implode('/', $pathParts) . '/trial/';
    $trialExtensionLink = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $trialPath;
    ?>
    <p>To register for a new trial, <a href="<?php echo esc_url($trialExtensionLink); ?>">click here</a>.</p>

    <p>Feel free to reach out with any questions, or book a time with our team directly, <a href="<?php echo esc_url($clickHereLink); ?>">here</a>.</p>

    <div class="signature-block"><?php echo $email_signature; ?></div>

</body></html>