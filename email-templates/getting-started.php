<html>
<body style='font-family: Arial, sans-serif; color:#333; line-height:1.6;'>
	
    <p>Dear <?php echo $first_name; ?>,</p>
    <p>Thank you for registering for your <i>LOTE4Kids</i> trial!</p>
    <p><i>LOTE4Kids</i> is the leading digital library of children's audio-picture books, designed for libraries and schools to support English and bilingual language learning through storytelling. All books are read aloud by native speakers (not AI), with English translations, reader levels, various reading formats, and built-in quizzes.</p>
    <p>To get the most from your trial, we've put together a few things to help you get started - just click the links below to explore:</p>
	<p>
		<table width="400" cellpadding="0" cellspacing="0" border="0" style="font-family: Arial, sans-serif; font-size: 14px; border-collapse: collapse; border: 1px solid #cccccc;">
			<thead>
				<tr>
					<td style="background-color: #b8e3dc; color: #222222; text-align: center; padding: 6px 14px; font-size: 14px; font-weight: bold;">Trial Progression</td>
				</tr>
			</thead>
			<tbody>
				<tr style="background-color: #ffffff;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;">1. <a href="<?php echo $checklist_flipbook__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_flipbook__text; ?></a></td>
				</tr>
				<tr style="background-color: #efefef;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;">2. <a href="<?php echo $checklist_quiz__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_quiz__text; ?></a></td>
				</tr>
				<tr style="background-color: #ffffff;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;">3. <a href="<?php echo $checklist_video__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_video__text; ?></a></td>
				</tr>
				<tr style="background-color: #efefef;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;">4. <a href="<?php echo $checklist_picture_card__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_picture_card__text; ?></a></td>
				</tr>
				<tr style="background-color: #ffffff;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;">5. <a href="<?php echo $checklist_non_fiction__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_non_fiction__text; ?></a></td>
				</tr>
				<tr style="background-color: #efefef;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;">6. <a href="<?php echo $checklist_sign_language__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_sign_language__text; ?></a></td>
				</tr>
				<tr style="background-color: #ffffff;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;">7. <a href="<?php echo $checklist_fun_facts__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_fun_facts__text; ?></a></td>
				</tr>
				<tr style="background-color: #efefef;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;">8. <a href="<?php echo $checklist_lekti__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_lekti__text; ?></a></td>
				</tr>
				<tr style="background-color: #ffffff;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;">9. <a href="<?php echo $checklist_activities__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_activities__text; ?></a></td>
				</tr>
				<tr style="background-color: #efefef;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;">10. <a href="<?php echo $checklist_mobile__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_mobile__text; ?></a></td>
				</tr>
				<tr style="background-color: #ffffff;">
					<td style="padding: 6px 14px; border-bottom: 1px solid #e0e0e0;">11. <a href="<?php echo $checklist_overview_video__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_overview_video__text; ?></a></td>
				</tr>
				<tr style="background-color: #efefef;">
					<td style="padding: 6px 14px;">12. <a href="<?php echo $checklist_staff_portal__link; ?>" style="color: #1a73e8; text-decoration: underline;"><?php echo $checklist_staff_portal__text; ?></a></td>
				</tr>
			</tbody>
		</table>
	</p>
	<p>Your 14-day trial details:</p>
    <ul>
        <li><strong>Website:</strong> <a href='<?php echo $lib_login_url; ?>'><?php echo $lib_login_url; ?></a></li>
        <li><strong>Mobile App:</strong> <a href='<?php echo $apple_link; ?>'>iOS</a> and <a href='<?php echo $google_link; ?>'>Android</a> (select <em><?php echo $library_name; ?></em>)</li>
        <li><strong>Access code:</strong> <?php echo $barcode; ?></li>
    </ul>
    <p>Feel free to reach out if you have any questions or would like pricing information.</p>
    <p>Happy exploring!</p>
 
	<div class="signature-block"><?php echo $email_signature; ?></div>

</body>
</html>