**OTC承兑商户接入RESTful API**


# 接入说明


## 请求地址

* 测试环境：
* 生产环境：


## 请求参数说明

所有接口请求参数仅包含以下两个参数：

* sign：签名信息
* context：使用 RSA 加密后的业务信息


## 安全认证

### 公钥和私钥

 GOW会提供三个密钥，分别为商户公钥、商户私钥、GOW平台公钥

### 详细步骤（以商户发起充值请求为例）

所有接口使用的都是是RSA加密、解密、签名,请求接口参数内容需要进行RSA加密以及根据加密内容生成签名，获取到返回结果则需要对签名进行验签以及对内容进行解密操作

#### 参数内容

商户按照相关接口将请求参数封装成json字符串contextPlain：
 ```
   {
       "userFlag":"0897564",
       "merchantCode":"000021",
       "timestamp":"1595214402990"
   }
   ```

#### 加密

使用GOW平台公钥对contextPlain进行加密生成context;

```java demo 
    String publicKey = "XXXXXXXXXXXX";
    // 对公钥解密
		byte[] keyBytes = Base64.decodeBase64(publicKey);
		// 取得公钥
		X509EncodedKeySpec x509KeySpec = new X509EncodedKeySpec(keyBytes);
		KeyFactory keyFactory = KeyFactory.getInstance(KEY_ALGORITHM);
		Key publicKey = keyFactory.generatePublic(x509KeySpec);
		// 对数据加密
		Cipher cipher = Cipher.getInstance(keyFactory.getAlgorithm());
		cipher.init(Cipher.ENCRYPT_MODE, publicKey);
		//分段加密
		byte[] enBytes = null;  
		for (int i = 0; i < contextPlain.getBytes().length; i += 64) {    
		// 注意要使用2的倍数，否则会出现加密后的内容再解密时为乱码  
		    byte[] doFinal = cipher.doFinal(ArrayUtils.subarray(contextPlain.getBytes(), i,i + 64));
		    enBytes = ArrayUtils.addAll(enBytes, doFinal);
		}
		return Base64.encodeBase64String(enBytes);
```

#### 签名

使用商户私钥对context进行签名生成sign;

```java demo 
    String privateKey = "XXXXXXXXXXX";
    byte[] publicInfo = null;
		try {
			Signature mySig = Signature.getInstance(SIGNATURE_ALGORITHM);// 用指定算法产生签名对象
			byte[] infomation=info.getBytes();
			// 解密由base64编码的私钥
			byte[] keyBytes = decryptBASE64(privateKey);
			// 构造PKCS8EncodedKeySpec对象
			PKCS8EncodedKeySpec pkcs8KeySpec = new PKCS8EncodedKeySpec(keyBytes);
			// KEY_ALGORITHM 指定的加密算法
			KeyFactory keyFactory = KeyFactory.getInstance(KEY_ALGORITHM);
			// 取私钥匙对象
			PrivateKey priKey = keyFactory.generatePrivate(pkcs8KeySpec);
			mySig.initSign(priKey); // 用私钥初始化签名对象
			mySig.update(infomation); // 将待签名的数据传送给签名对象
			publicInfo = mySig.sign(); // 返回签名结果字节数组
		} catch (Exception e) {
			e.printStackTrace();
		}
		 return Base64.encodeBase64String(publicInfo);
```

#### 组装如下数据请求接口，判断请求是否成功

```
{
       "sign": "XXXXXX",
       "context": "XXXXXX"
   }
   ```

#### 验签

使用GOW平台公钥对返回的签名sign进行验签;

```java demo 
    boolean verify = false;
		try {
			byte[] infomation=context.getBytes();
			byte[] publicInfo= decryptBASE64(sign);
			Signature mySig = Signature.getInstance(SIGNATURE_ALGORITHM);//用指定算法产生签名对象
			byte[] keyBytes = Base64.decodeBase64(publicKey);
			// 构造X509EncodedKeySpec对象
			X509EncodedKeySpec keySpec = new X509EncodedKeySpec(keyBytes);
			// KEY_ALGORITHM 指定的加密算法
			KeyFactory keyFactory = KeyFactory.getInstance(KEY_ALGORITHM);
			// 取公钥匙对象
			PublicKey pubKey = keyFactory.generatePublic(keySpec);
			mySig.initVerify(pubKey); // 使用公钥初始化签名对象,用于验证签名
			mySig.update(infomation); // 更新签名内容
			verify = mySig.verify(publicInfo); // 得到验证结果
		} catch (Exception e) {
			e.printStackTrace();
		}
		return verify;
```

#### 解密

使用商户私钥对返回的内容context进行解密;

```java demo 
    byte[] data = Base64.decodeBase64(context);
    // 对密钥解密
		byte[] keyBytes = Base64.decodeBase64(privateKey);
		// 取得私钥
		PKCS8EncodedKeySpec pkcs8KeySpec = new PKCS8EncodedKeySpec(keyBytes);
		KeyFactory keyFactory = KeyFactory.getInstance(KEY_ALGORITHM);
		Key privateKey = keyFactory.generatePrivate(pkcs8KeySpec);
		// 对数据解密
		Cipher cipher = Cipher.getInstance(keyFactory.getAlgorithm());
		cipher.init(Cipher.DECRYPT_MODE, privateKey);
		StringBuilder sb = new StringBuilder();  
		for (int i = 0; i < data.length; i += 128) {  
		    byte[] doFinal = cipher.doFinal(ArrayUtils.subarray(data, i, i + 128));
		    sb.append(new String(doFinal,"UTF-8"));  
		}  
		return sb.toString();
```

# 接口信息


## 充值

**接口地址** ``


**请求方式** `GET`


**consumes** ``


**produces** `["*/*","application/json"]`


**接口描述** ``

**请求参数**

| 参数名称         | 参数说明     |     长度 |  是否必须      |  数据类型   |  schema  |
| ------------ | -------------------------------- |-----------|--------|----|--- |
| userFlag         |      用户标识   |     40        |       true      | string   |      |
| merchantCode         |      商户编号（由GOW给出）   |     40        |       true      | string   |      |
| timestamp         |      请求接口时刻的当前时间戳(毫秒)   |     13        |       true      | long   |      |




**响应状态**

| 状态码         | 说明                             |    schema                         |
| ------------ | -------------------------------- |---------------------- |
| 200         | OK                        |响应结果                          |
| 401         | Unauthorized                        |                          |
| 403         | Forbidden                        |                          |
| 404         | Not Found                        |                          |


# 
