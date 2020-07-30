<?php
function curl($url,$postdata=[],$reqmethod) {
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, $url);
	if ($reqmethod == 'POST') {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
	}
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_HEADER,0);
	curl_setopt($ch, CURLOPT_TIMEOUT,60);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
	curl_setopt ($ch, CURLOPT_HTTPHEADER, [
		"Content-Type: application/json",
		]);
	$output = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);
	return $output;
}

function encrypt($originalData, $publicKey){
    $crypto = '';
    foreach (str_split($originalData, 64) as $chunk) {
        openssl_public_encrypt($chunk, $encryptData, $publicKey);
        $crypto .= $encryptData;
    }
    return base64_encode($crypto);
}

function decrypt($encryptData, $privateKey){
    $crypto = '';
    foreach (str_split(base64_decode($encryptData), 128) as $chunk) {
        openssl_private_decrypt($chunk, $decryptData, $privateKey);
        $crypto .= $decryptData;
    }
    return $crypto;
}

# GOW公钥
$platformPublicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCP1IcTLjZ1tdbY7y+PDnN33KYtdgKDdwrwryXxPU0uQRIMbovdaIaC39izIYvplOxbnXMY9e4SY5PFCE9N4CFne2sbRYM4xHR+Gla9oJadSaIkzH4yWTEVrOv08Q1S1+0vhnkEpdNUFg70GBfnH6nEzhX+rsJO1BWlv9fUOoThPQIDAQAB';
# GOW私钥
$platformPrivateKey = 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAI/UhxMuNnW11tjvL48Oc3fcpi12AoN3CvCvJfE9TS5BEgxui91ohoLf2LMhi+mU7Fudcxj17hJjk8UIT03gIWd7axtFgzjEdH4aVr2glp1JoiTMfjJZMRWs6/TxDVLX7S+GeQSl01QWDvQYF+cfqcTOFf6uwk7UFaW/19Q6hOE9AgMBAAECgYB/zO9s6p43f6j1P6r5qXSOZ8A9GuPm3ssYy/ih37+JvwYDh+K9jJghCDfsC33fwpU2XrQb1MKDEnoGFHkrEGF804GLryS4Bb+nKz5ufJGt+4oCHWKP4hLzHD5BZI/pIdaXcQvJuCacqjXOyCvaxHGegPk43HhpsH3xC7dd+nJhvQJBANfDA7mtoahWpiXeYLMVeJ4tOujczrU1/k7qi/Bu1kAa19BMX8MmP+RChVMpovLXY4Z0OCLEwH3zXmcdg6cm5UcCQQCqp1OCxsrLCSWeFEFlD5s15BWlhNXNCXvAho/UcgaKWCWAJjjs7SmA3GZEaVB4Qfr/LROZZb9/s3IHt+uSKBdbAkACRmEcrRL+RmOcFJsqaDiMWme7mtBnIrmatWhiUZjati2+WX/M+/NCgd0MAm1gaBr5iPIqk65/5XgCWFJSCdvzAkAqAwN2IIZwMZJHNmla/dqSC9KgavFPhtQmc3oZLPEbQdQJll9RRJmBFcE/ekXLNUMbavd2PixEveKGr+qYkrIdAkEAuOxgM8RMjyTtAfgJSjhGNvEugdXZ2BnQArNV9QZfri0YBattalpNO4hLV6koSJmVI+/aCZLspS1/ZYyIj/1Hgw==';

# 商户公钥
$merchantPubKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCHuNBmiYccFGcROIP0MVGLkQ45sY5DQ1wxFuQe0nO/EAEFaUz+QdE47ETL4aj+IhI9DIPD3BF1XxYCZcQze4Jqhfch29nWbwgY3VW5JXMgeWwGYot6RfnB/L9sQmM448x7gjziCbd14cSeekRblBATf81VCoAXlsjIdkkYYr1SIwIDAQAB';
# 商户私钥
$privateKey = 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAIe40GaJhxwUZxE4g/QxUYuRDjmxjkNDXDEW5B7Sc78QAQVpTP5B0TjsRMvhqP4iEj0Mg8PcEXVfFgJlxDN7gmqF9yHb2dZvCBjdVbklcyB5bAZii3pF+cH8v2xCYzjjzHuCPOIJt3XhxJ56RFuUEBN/zVUKgBeWyMh2SRhivVIjAgMBAAECgYBRFj8Uc+hQA3SMmlpj469XlkOMfqODVlRXU9jY/V1/8lzZ0c7ZPZN10dtMgDcKFmsVJEv5EZswp62rowd0xr7kbQZkmC30C7YjM5lUpbVFWiH6sW/P3fMXasKwWxVMVTGxUt9quWYBJ42L+3A+AgGd/Ysem5tdDvklMoaeO9h8SQJBALvbZmrfSS9FXVGLhyOayqkvu50EgzIRNyAQkoPAgmYzKwSzfqBarBGMM6wsoCxuAk+NQjC1rbW65pYlaOzGz0UCQQC49BgVLGWapwL1BHEbOFPUaUTYZ+7pyJaXpyKD8fcXLdm8HwkQAf1oli1GYSiiscCKLMJQ5VBHuj0zvlbwLN5HAkEAmKMiSn/2tQQFWPan7VQeiu2P4XsDJrE6O0F76rWGvoeg0ocNwjkqSm/CpIj19GPGWOEMAQv9gwXDygfHg2veiQJAb16UTdOhDuH4Vt+o1/IwEFyfFwxmgaHGhGUg1IDT/8IdNTke9OOt2tdrRdDlbipIvSs8iwe6MqbDia/Ym+D4qwJAbwJBkFRk5DrzLGgg0ifaKhZnBOoUKH1pYNJQRWeges5kjwAsseNMePCOz4y/8zJaSFavWey39ErQUv/dOHzfTw==';

$platformPublicKey = chunk_split($platformPublicKey, 64, "\n");
$platformPublicKey = "-----BEGIN PUBLIC KEY-----\n$platformPublicKey-----END PUBLIC KEY-----\n";
echo '$platformPublicKey：'.$platformPublicKey."\n";

$platformPrivateKey = chunk_split($platformPrivateKey, 64, "\n");
$platformPrivateKey = "-----BEGIN RSA PRIVATE KEY-----\n$platformPrivateKey-----END RSA PRIVATE KEY-----\n";
echo '$platformPrivateKey：'.$platformPrivateKey."\n";

$privateKey = chunk_split($privateKey, 64, "\n");
$privateKey = "-----BEGIN RSA PRIVATE KEY-----\n$privateKey-----END RSA PRIVATE KEY-----\n";
echo '$privateKey：'.$privateKey."\n";

$merchantPubKey = chunk_split($merchantPubKey, 64, "\n");
$merchantPubKey = "-----BEGIN PUBLIC KEY-----\n$merchantPubKey-----END PUBLIC KEY-----\n";
echo '$merchantPubKey：'.$merchantPubKey."\n";

$data = array(
    'userFlag' => 'xxxxxxx',
    'timestamp' => '1596090095000'
);

# 签名并加密
$dataStr = json_encode($data, JSON_UNESCAPED_UNICODE);
echo '$dataStr：'.$dataStr."\n";
$context = encrypt($dataStr, $platformPublicKey);
openssl_sign($context, $sign, $privateKey, OPENSSL_ALGO_MD5);
$sign = base64_encode($sign);
$postdata = array(
    'sign' => $sign,
    'context' => $context,
    'merchantCode' => '1001'
);

echo '签名：'.$sign."\n";
echo '加密内容：'.$context."\n";


# 验证签名
if (openssl_verify($context, base64_decode($sign), $merchantPubKey, OPENSSL_ALGO_MD5)) {
	# 解密
    $context = decrypt($context, $platformPrivateKey);
    echo '操作成功，数据：'.$context;
} else {
    echo '验签不通过';
}
?>