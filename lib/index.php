
<form id="form58" name="form58" class="wufoo topLabel page form-ajax" accept-charset="UTF-8" autocomplete="off" enctype="multipart/form-data" method="post">
		<h1>Book your appointment today!</h1>
		<div class="row">
			<div class="col-md-4">
				<input id="Field2" name="Field2" type="text" value="<?php echo isset($_POST['Field2']) ? $_POST['Field2']: '' ?>" placeholder="Name" maxlength="255" tabindex="1" onkeyup="" required="" aria-required="true">
			</div>
			<div class="col-md-4">
				<input id="Field3" name="Field3" type="text" value="<?php echo isset($_POST['Field3']) ? $_POST['Field3']: '' ?>" placeholder="Email" maxlength="255" tabindex="1" onkeyup="" required="" aria-required="true">
			</div>
			<div class="col-md-4">
				<input id="Field4" name="Field4" type="text" value="<?php echo isset($_POST['Field4']) ? $_POST['Field4']: '' ?>" placeholder="Phone Number" maxlength="255" tabindex="1" onkeyup="" required="" aria-required="true">
			</div>
			<div class="col-md-12">
				<textarea placeholder="Message or Notes" id="Field14" name="Field14" spellcheck="true" onkeyup=""><?php echo isset($_POST['Field14']) ? $_POST['Field14']: '' ?></textarea>
			</div>
			<div class="col-md-12">
				<div class="msg"></div>
			</div>
			<div class="col-md-12 text-center">
				<button id="saveForm" name="saveForm" lang="en" class="btTxt submit" type="submit" value="Submit">SEND</button>
			</div>
			<div style="display:none;">
				<input id="Field6" name="Field6" type="text" class="utm_source" value="" maxlength="255" tabindex="5" onkeyup="">
				<input id="Field8" name="Field8" type="text" class="utm_medium" value="" maxlength="255" tabindex="6" onkeyup="">
				<input id="Field9" name="Field9" type="text" class="utm_campaign" value="" maxlength="255" tabindex="7" onkeyup="">
				<input id="Field11" name="Field10" type="text" class="utm_term" value="" maxlength="255" tabindex="8" onkeyup="">
				<input id="Field12" name="Field11" type="text" class="utm_content" value="" maxlength="255" tabindex="9" onkeyup="">
				<input id="Field16" name="Field16" type="text" class="field text medium" value="#success" onkeyup="">
				<label for="comment">Do Not Fill This Out</label>
				<textarea name="comment" id="comment" rows="1" cols="1"></textarea>
				<input type="hidden" id="idstamp" name="idstamp" value="NP4MZzm7V4Bv7XjAucHViyJcOOpCRotOZcrHuViCGAQ=">
			</div>
		</div>
	</form>
<?php
if(!empty($_POST)){
    if(extension_loaded('curl')){
        require_once './Form.php';

        $form = new Form('drpatrickweightloss', 2, 'en');

        $fields = array(
            'key' =>  '8u0swxx6js4kgsocs4ggososc4gcgk8',
            'field_1' =>  $_POST['Field2'],
            'field_2' => $_POST['Field4'],
            'field_4' => $_POST['Field3'],
            'field_3' => $_POST['Field14'],
            'field_14' => '',
            'utm_source' => $_POST['Field6'],
            'utm_medium' => $_POST['Field8'],
            'utm_campaign' => $_POST['Field9'],
            'utm_term' => $_POST['Field10'],
            'utm_content' => $_POST['Field11'],
        );

        print_r($form->submit($fields));
    }else{
        echo "Error: To submit the form remotely, CURL extension should be enabled and active on this webserver.";
    }
}
?>
