<!DOCTYPE html>
<html>
   <head>
      <title>Reference Fields for PHP Include</title>
      <meta charset="UTF-8">
      <!--<meta name="viewport" content="width=device-width, initial-scale=1.0">-->
   </head>
   <body>

	<fieldset class="group">
		<legend>References</legend>

		<div class="input" style="border-top: none;">
			<label for="Reference1">Coach Reference
				<?php if (isset($includeReferenceFields) && $includeReferenceFields == "Mandatory") {echo "*";} ?>
			</label>
			<div class="rightcolumn">
				<table style="width: 100%; max-width: 400px; padding: 0; margin: 0;">
					<tr style="width: auto;">
						<td style="padding: 0;">
							<fieldset class="field">
								<legend>First</legend>
								<input name="referenceFirstName1" type="text" size="16" id="Reference1" title="First name of your first reference."
									<?php recallText((empty($referenceFirstName1) ? "" : $referenceFirstName1), (isset($includeReferenceFields) && $includeReferenceFields == "Mandatory" ? "yes" : "no")); ?> />
							</fieldset>
						</td>
						<td>
							<fieldset class="field">
								<legend>Last</legend>
								<input name="referenceLastName1" type="text" size="16" title="Last name of your first reference."
									<?php recallText((empty($referenceLastName1) ? "" : $referenceLastName1), (isset($includeReferenceFields) && $includeReferenceFields == "Mandatory" ? "yes" : "no")); ?> />
							</fieldset>
						</td>
					</tr>
					<tr style="width: auto;">
						<td style="padding: 0;">
							<fieldset class="field">
								<legend>Phone</legend>
								<input name="referencePhone1" type="text" size="16" title="Phone number of your first reference."
									<?php recallText((empty($referencePhone1) ? "" : $referencePhone1), (isset($includeReferenceFields) && $includeReferenceFields == "Mandatory" ? "yes" : "no")); ?> />
							</fieldset>
						</td>
						<td>
							<fieldset class="field">
								<legend>E-Mail</legend>
								<input name="referenceEmail1" type="text" size="40" title="E-mail of your first reference."
									<?php recallText((empty($referenceEmail1) ? "" : $referenceEmail1), (isset($includeReferenceFields) && $includeReferenceFields == "Mandatory" ? "yes" : "no")); ?> />
							</fieldset>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="input">
			<label for="Reference2">Reference 2</label>
			<div class="rightcolumn">
				<table style="width: 100%; max-width: 400px; padding: 0; margin: 0;">
					<tr style="width: auto;">
						<td style="padding: 0;">
							<fieldset class="field">
								<legend>First</legend>
								<input name="referenceFirstName2" type="text" size="16" id="Reference2" title="First name of your first reference."
									<?php recallText((empty($referenceFirstName2) ? "" : $referenceFirstName2), "no"); ?> />
							</fieldset>
						</td>
						<td>
							<fieldset class="field">
								<legend>Last</legend>
								<input name="referenceLastName2" type="text" size="16" title="Last name of your first reference."
									<?php recallText((empty($referenceLastName2) ? "" : $referenceLastName2), "no"); ?> />
							</fieldset>
						</td>
					</tr>
					<tr style="width: auto;">
						<td style="padding: 0;">
							<fieldset class="field">
								<legend>Phone</legend>
								<input name="referencePhone2" type="text" size="16" title="Phone number of your first reference."
									<?php recallText((empty($referencePhone2) ? "" : $referencePhone2), "no"); ?> />
							</fieldset>
						</td>
						<td>
							<fieldset class="field">
								<legend>E-Mail</legend>
								<input name="referenceEmail2" type="text" size="40" title="E-mail of your first reference."
									<?php recallText((empty($referenceEmail2) ? "" : $referenceEmail2), "no"); ?> />
							</fieldset>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="input">
			<label for="Reference3">Reference 3</label>
			<div class="rightcolumn">
				<table style="width: 100%; max-width: 400px; padding: 0; margin: 0;">
					<tr style="width: auto;">
						<td style="padding: 0;">
							<fieldset class="field">
								<legend>First</legend>
								<input name="referenceFirstName3" type="text" size="16" id="Reference3" title="First name of your first reference."
									<?php recallText((empty($referenceFirstName3) ? "" : $referenceFirstName3), "no"); ?> />
							</fieldset>
						</td>
						<td>
							<fieldset class="field">
								<legend>Last</legend>
								<input name="referenceLastName3" type="text" size="16" title="Last name of your first reference."
									<?php recallText((empty($referenceLastName3) ? "" : $referenceLastName3), "no"); ?> />
							</fieldset>
						</td>
					</tr>
					<tr style="width: auto;">
						<td style="padding: 0;">
							<fieldset class="field">
								<legend>Phone</legend>
								<input name="referencePhone3" type="text" size="16" title="Phone number of your first reference."
									<?php recallText((empty($referencePhone3) ? "" : $referencePhone3), "no"); ?> />
							</fieldset>
						</td>
						<td>
							<fieldset class="field">
								<legend>E-Mail</legend>
								<input name="referenceEmail3" type="text" size="40" title="E-mail of your first reference."
									<?php recallText((empty($referenceEmail3) ? "" : $referenceEmail3), "no"); ?> />
							</fieldset>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</fieldset>

   </body>
</html>