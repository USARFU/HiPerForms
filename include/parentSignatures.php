<!DOCTYPE html>
<html>
   <head>
      <title>Parent Signature for PHP Include</title>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
   </head>
   <body>

	<fieldset class="group">
		<legend>Parental / Guardian Approval</legend>
         
      <div style="width: 96%; display: block; margin-left: auto; margin-right: auto;">
         <h3>Informed Consent and Acknowledgment</h3>
         <p>
            I hereby give my approval for my child's participation in any and all activities prepared by USA Rugby during the camp, tour or assembly.
            In exchange for the acceptance of said child’s candidacy by USA Rugby, I assume all risk and hazards incidental to the conduct of the activities, 
            and release, absolve and hold harmless USA Rugby and all its respective officers, agents, and representatives from any and all liability for injuries 
            to said child arising out of traveling to, participating in, or returning from selected USA Rugby All-American assemblies.
         </p>
         <p>
            In case of injury to said child, I hereby waive all claims against USA Rugby, including all coaches and affiliates, all participants, sponsoring agencies,
            advertisers, and, if applicable, owners and lessors of premises used to conduct the event. There is a risk of being injured that is inherent in all sports activities, 
            including rugby. Some of these injuries include, but are not limited to, the risk of fractures, paralysis, or death.
         </p>

         <div style="padding: 20px">
            <div style="margin-bottom: 4px">Informed Consent and Acknowledgment - Parent Signature*</div>
            <!-- Signature Field -->
            <div id="signature"></div>
            
            <?php
            ### Show existing signature as image below signature field ###
            if($signatureConsentLength > 25 || !empty($signatureConsent)){
               echo '
                  <div style="margin-top: 6px; position: relative; height: auto; width: auto; overflow: hidden;">
                     Existing Signature (A new Signature will replace this):<br />
                     <img style="max-width: 100%; max-height: 100%" src="data:image/png;base64,' . $signatureConsent . '">
                  </div>';
            }
            ?>
         </div>
         
         <script>
            $(document).ready(function() {
               var $sigdiv = $("#signature");
               $sigdiv.jSignature({'UndoButton':true});
            });
         </script>
         
         <h3>Medical Release and Authorization</h3>
         <p>
            As Parent and/or Guardian of the named athlete, I hereby authorize the diagnosis and treatment by a qualified and licensed medical professional, 
            of the minor child, in the event of a medical emergency, which in the opinion of the attending medical professional, 
            requires immediate attention to prevent further endangerment of the minor’s life, physical disfigurement, physical impairment, 
            or other undue pain, suffering or discomfort, if delayed.
         </p>
         <p>
            Permission is hereby granted to the attending physician to proceed with any medical or minor surgical treatment, 
            x-ray examination and immunizations for the named athlete. In the event of an emergency arising out of serious illness, 
            the need for major surgery, or significant accidental injury, I understand that every attempt will be made by the USA Rugby staff 
            to contact me in the most expeditious way possible. This authorization is granted only after a reasonable effort has been made to reach me.
         </p>
         <p>
            Permission is also granted to USA Rugby and its affiliates including coaches,
            trainers and medical staff to provide the needed emergency treatment prior to the child’s admission to the medical facility.
         </p>
         <p>
            Release authorized on the dates and/or duration of the registered season.
         </p>
         <p>
            This release is authorized and executed of my own free will, with the sole purpose of authorizing medical treatment under 
            emergency circumstances, for the protection of life and limb of the named minor child, in my absence.
         </p>

         <div style="padding: 20px">
            <div style="margin-bottom: 4px">Medical Release and Authorization - Parent Signature*</div>
            <!-- Signature Field -->
            <div id="signature2"></div>
            
            <?php
            ### Show existing signature as image below signature field ###
            if($signatureMedicalLength > 25 || !empty($signatureMedical)){
               echo '
                  <div style="margin-top: 6px; position: relative; height: auto; width: auto; overflow: hidden;">
                     Existing Signature (A new Signature will replace this):<br />
                     <img style="max-width: 100%; max-height: 100%" src="data:image/png;base64,' . $signatureMedical . '">
                  </div>';
            }
            ?>
         </div>
         
         <script>
            $(document).ready(function() {
               var $sigdiv = $("#signature2");
               $sigdiv.jSignature({'UndoButton':true});
            });
         </script>
         
         <h3>Code of Conduct</h3>
         <p>
            As a participant of a USA Rugby All-American assembly, camp or tour I promise that I will:
         </p>
         <ol>
            <li>Show good character. Be honest, trustworthy, responsible, unselfish and a credit to my family and my country.</li>
            <li>Respect myself, teammates, coaches, officials, opponents and the game.</li>
            <li>Follow team rules at all times.</li>
            <li>Work to achieve a high level of fitness and game preparedness.</li>
            <li>Give my best effort at all times.</li>
            <li>Work hard towards Team goals, and help teammates reach those goals.</li>
            <li>Declare the use of any and all medications, nor use tobacco, alcohol, illegal drugs, or banned performance enhancing substances.</li>
            <li>Not engage in any criminal conduct, including, but not limited to laws governing the possession, provision and use of drugs, and or alcohol.</li>
         </ol>
         <p>
            <span style="font-weight:bold;">Violations of the code can result in one or more of the following:</span>
         </p>
         <ol type="a">
            <li><span style="font-weight:bold;">Dismissal from the event and sent home immediately</span> (additional travel costs will be the responsibility of the player and his family).</li>
            <li><span style="font-weight:bold;">Suspension</span> - ranging from a game suspension to more where a player will not compete for a specified period of time or number of games. This can include a suspension of CIPP.</li>
            <li><span style="font-weight:bold;">Community Service.</span> The player will complete a determined number of hours in a recommended community service project after the event.</li>
         </ol>
         <p>
            By signing I pledge to uphold the spirit of this Code which offers a general guide to my behavior and conduct. 
            I acknowledge that I have a right to a hearing if my opportunity to compete is denied for any reason or if I’m charged with any violation of the Code. 
            That hearing will be within 24 hours of any alleged violations. The hearing committee will include the Head Coach, assistant coach and Team Manager. 
            The decision of the committee will be final.
         </p>
         
         <div style="padding: 20px">
            <div style="margin-bottom: 4px">Code of Conduct - Player Signature*</div>
            <!-- Signature Field -->
            <div id="signature3"></div>
            
            <?php
            ### Show existing signature as image below signature field ###
            if($sigConductPlayerLength > 25 || !empty($sigConductPlayer)){
               echo '
                  <div style="margin-top: 6px; position: relative; height: auto; width: auto; overflow: hidden;">
                     Existing Signature (A new Signature will replace this):<br />
                     <img style="max-width: 100%; max-height: 100%" src="data:image/png;base64,' . $sigConductPlayer . '">
                  </div>';
            }
            ?>
         </div>
         
         <script>
            $(document).ready(function() {
               var $sigdiv = $("#signature3");
               $sigdiv.jSignature({'UndoButton':true});
            });
         </script>
         
         <div style="padding: 20px">
            <div style="margin-bottom: 4px">Code of Conduct - Parent Signature*</div>
            <!-- Signature Field -->
            <div id="signature4"></div>
            
            <?php
            ### Show existing signature as image below signature field ###
            if($sigConductParentLength > 25 || !empty($sigConductParent)){
               echo '
                  <div style="margin-top: 6px; position: relative; height: auto; width: auto; overflow: hidden;">
                     Existing Signature (A new Signature will replace this):<br />
                     <img style="max-width: 100%; max-height: 100%" src="data:image/png;base64,' . $sigConductParent . '">
                  </div>';
            }
            ?>
         </div>
         
         <script>
            $(document).ready(function() {
               var $sigdiv = $("#signature4");
               $sigdiv.jSignature({'UndoButton':true});
            });
         </script>
         
         <h3>Media Release</h3>
         <p>
            I grant designated HSAA Staff permission to use photograph(s) or video of my child for purposes of publicity or advertising on platforms such as website, 
            Facebook, advertisements, social media, or brochures.  By electing to have your child be part of an HSAA sponsored event you are aware that your child’s 
            likeness may be used and that their name may also be used. HSAA elects to use the full name of your child in order to identify them for possible scouting 
            or selection to representative sides such as the High School All Americans.
         </p>
         <p>
            If the parent or guardian requests that their player’s likeness not be used then they need to notify the Head Coach or Team Manager prior to any USA Rugby All-American event.
         </p>
         
         <div style="padding: 20px">
            <div style="margin-bottom: 4px">Media Release - Parent Signature*</div>
            <!-- Signature Field -->
            <div id="signature5"></div>
            
            <?php
            ### Show existing signature as image below signature field ###
            if($sigMediaReleaseLength > 25 || !empty($sigMediaRelease)){
               echo '
                  <div style="margin-top: 6px; position: relative; height: auto; width: auto; overflow: hidden;">
                     Existing Signature (A new Signature will replace this):<br />
                     <img style="max-width: 100%; max-height: 100%" src="data:image/png;base64,' . $sigMediaRelease . '">
                  </div>';
            }
            ?>
         </div>
         
         <script>
            $(document).ready(function() {
               var $sigdiv = $("#signature5");
               $sigdiv.jSignature({'UndoButton':true});
            });
         </script>
         
         <h3>Confirmation</h3>
         <div <?php if(empty($waiver)) { echo 'class="missing" style="padding: 1em"'; } ?> >
               <input type='checkbox' name='waiver' value='1' title="Acknowlege form completness." <?php if ($waiver == 1){ echo'checked="checked"'; } ?> />
               BY ACKNOWLEDGING AND SIGNING, I AM DELIVERING AN ELECTRONIC SIGNATURE THAT WILL HAVE THE SAME EFFECT AS AN ORIGINAL MANUAL PAPER SIGNATURE. 
               THE ELECTRONIC SIGNATURE WILL BE EQUALLY AS BINDING AS AN ORIGINAL MANUAL PAPER SIGNATURE.
               <br /><br />
               I ACCEPT RESPONSIBILITY THAT THE INFORMATION PROVIDED ON THIS FORM IS ACCURATE.
         </div>
      </div>

		</fieldset>
   </body>
</html>