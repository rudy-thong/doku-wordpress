<?php
	
	require 'configure.php';
	
	$req_basket = $item . '|' . $price . '|' . $quantity . '|' . ($price * $quantity);
	$req_invoice_no = $merchant_code . date('YmdHis');
	$req_recipient_name = $_POST['name'];
	$req_email_recipient = $_POST['email'];
	$req_email_recipient_cc = $adminEmail;
	$req_amount = ($price * $quantity);
	$req_address = preg_replace("/[^a-zA-Z0-9]+/", "", $_POST['email']);
	$req_invoice_date = date('d-m-y');
	if ($timelimitInMinute > 0) {
		$req_valid_date = date('d-m-Y H:i:s', strtotime('+' . $timelimitInMinute . ' minutes', time()));
	} 
	$req_send_email = 1;
	$req_send_success_payment = 1;
	$req_merchant_id = $merchant_id;
	$req_words = SHA1($req_invoice_no . $req_amount . $req_merchant_id . $shared_key);

	if($environment == 'Production') {
		$url = 'https://paybuddy.doku.com/api/orders';
	} else {
		$url = 'http://devpaybuddy.doku.com/api/orders';
	}
	$fields = array(
            'req_basket'=>urlencode($req_basket),
            'req_invoice_no'=>urlencode($req_invoice_no),
            'req_recipient_name'=>urlencode($req_recipient_name),
            'req_email_recipient'=>urlencode($req_email_recipient),
            'req_email_recipient_cc'=>urlencode($req_email_recipient_cc),
            'req_amount'=>urlencode($req_amount),
            'req_address'=>urlencode($req_address),
            'req_invoice_date'=>urlencode($req_invoice_date),
            'req_valid_date'=>urlencode($req_valid_date),
            'req_send_email'=>urlencode($req_send_email),
            'req_send_success_payment'=>urlencode($req_send_success_payment),
            'req_merchant_id'=>urlencode($req_merchant_id),
            'req_words'=>urlencode($req_words)
     );

	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	$fields_string = rtrim($fields_string,'&');

	$ch = curl_init();

	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POST,count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

	$result = curl_exec($ch);
	
	$data = json_decode($result);
	
	if($data->res_response_code == '0000') {
		header("Location: " . $successLink); 
		echo "<script>alert('Payment Mail has been successfully sent to ' + " . $req_email_recipient . ")</script>";
	} else {
		header("Location: " . $failedLink); 
		echo "<script>alert('Submit process failed.')</script>";
	}
?>		

?>