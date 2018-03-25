<?php
	$data = openssl_random_pseudo_bytes(20);
	
	function totpCode($secret, $timeCode, $digits=6){
		$hmac=hash_hmac("sha1", $secret, $timeCode, true);
		//get the last byte as an unsigned cahr, but only keep the low 4 bits
		$offset = unpack("C", substr($hmac,19,1))[1] & 0xf;
		//get 4 bytes at offset as little-endian integer, then drop high bit
		$word = unpack("N", substr($hmac,$offset,4))[1] & 0x7fffffff;
		$code = $word % 10**$digits;
		return $code;
	}
	function totpCodeNow($secret, $digits = 6, $chunkSize = 30, $epoch = 0){
		$timeCode = floor((time()-$epoch)/$chunkSize);
		return totpCode($secret, $timeCode, $digits);
	}
	function totpCodeVerify($secret, $codeIn, $digits = 6,
		$chunkSize=30, $epoch=0){
		$verified=false;
		$timeCode = floor((time()-$epoch)/$chunkSize);
		$codeNow=totpCode($secret, $timeCode, $digits);
		$codePrev=totpCode($secret, $timeCode-1, $digits);
		$codeNext=totpCode($secret, $timeCode+1, $digits);
		if($codeIn==$codeNow){
			$verified=true;
		}
		if($codeIn==$codePrev){
			$verified=true;
		}
		if($codeIn==$codeNext){
			$verified=true;
		}
		return $verified;
	}
?>