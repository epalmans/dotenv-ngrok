# dotenv-ngrok
This little PHP-script updates your project's .env files with the URL for the tunnels that have been set by your local [ngrok](https://ngrok.com/) install. For instance, to use on a Laravel 5 (development) environment.

## Why?
Often 3rd party integration with your project, utilizes webhooks to callback to your app. E.g. payment providers that signal a transaction has been fully processed.

In production environments such endpoints are often exposed publicly through a fixed public IP.  
However, on a development/test environment your are likely 'hidden' behind a NAT-device or change internetconnections when working at different locations.  
This is where ngrok comes into play wonderfully: it assigns a dynamic URL for your current connection that'll then be acccessible by the outside world. Since it sets up a tunnel, there is no need for upnp/portfowarding/NAT rules.

On making API calls from your app, you can often specify which URL the webhook should call back on.  
When testing/developing, you obviously want this to be addressed to your development PC/laptop as you can then further process the webhook as soon as it comes in.  
Since the ngrok URL's change at every start, I wrote this script to reflect those changes in my .env files where I'd usually store my API/webhook config in.

## Usage
### Prepare .env file
Instead of hard-coding a webhook URL for your API call to use, add it to your .env like this:

```
NGROK_HTTP=
NGROK_HTTPS=

PAYMENTPROVIDER_URL=https://api.somepaymentprovider.com/pay
PAYMENTPROVIDER_KEY=somekindofauthenticationkey
PAYMENTPROVIDER_WEBHOOK=${NGROK_HTTPS}/hooks/payment
```

Note: Setting `NGROK_HTTPS=` and using it by specifying `${NGROK_HTTPS}` is the key here.

### Run script
After ngrok is running, start `ngrok-dotenv-update.php` by passing the path to your .env-file as an argument ($argv[1]).

##### Tip: automating this (on Windows):
Create a batch file that will start ngrok and update your project's .env.

```
@echo off

start "ngrok" /MIN "ngrok" http -host-header=myapp.url 80	& :: start ngrok

timeout 5 > NUL							& :: allow ngrok a little time to setup its tunnels

php -f ngrok-dotenv-update.php "C:\path\to\myapp\.env"		& :: update .env
```
