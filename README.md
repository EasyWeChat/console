<h1 align="center"> EasyWeChat Console Tool </h1>

<p align="center">A command line for EasyWeChat Application.</p>

## Installing

```shell
$ composer require easywechat/console -vvv
```

Or install it globally:

```shell
$ composer global require easywechat/console -vvv
```

## Usage

```shell
$ ./vendor/bin/easywechat list

# globally install
$ easywechat list
```

### Get payment RSA public key.

https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=24_7

```shell
$ ./vendor/bin/easywechat payment:rsa_public_key \
    --mch_id=14339221228 \
    --api_key=36YTbDmLgyQ52noqdxgwGiYy \
    --cert_path=/Users/overtrue/www/demo/apiclient_cert.pem \
    --key_path=/Users/overtrue/www/demo/apiclient_key.pem 
    
# Public key of mch_id:14339221228 saved as ./public-14339221228.pem
```

### List, Create, Delete Official Account menu structure.

https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141013  
https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141014  
https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141015  

```shell
$ ./vendor/bin/easywechat menus:list_menus \
    --app_id=14339221228 \
    --secret=36YTbDmLgyQ52noqdxgwGiYy \
    --token=mytoken \
    --aes_key=0xp26B0rqlKFZPWbr5lQs4SVaugBnFjE0H1xE9rfePX
    --file_output=/Users/overtrue/www/demo/menus.json 
    
# JSON menu structure of the Official Account output in /Users/overtrue/www/demo/menus.json
```

```shell
$ ./vendor/bin/easywechat menus:create_menus \
    --app_id=14339221228 \
    --secret=36YTbDmLgyQ52noqdxgwGiYy \
    --token=mytoken \
    --aes_key=0xp26B0rqlKFZPWbr5lQs4SVaugBnFjE0H1xE9rfePX
    --file=/Users/overtrue/www/demo/menus.json 
    
# Menu structure of the Official Account created/updated
```

```shell
$ ./vendor/bin/easywechat menus:delete_menus \
    --app_id=14339221228 \
    --secret=36YTbDmLgyQ52noqdxgwGiYy \
    --token=mytoken \
    --aes_key=0xp26B0rqlKFZPWbr5lQs4SVaugBnFjE0H1xE9rfePX
    --save=/Users/overtrue/www/demo/menus.json 
    
# Menu structure of the Official Account deleted
# JSON menu structure saved as /Users/overtrue/www/demo/menus.json
```

## License

MIT