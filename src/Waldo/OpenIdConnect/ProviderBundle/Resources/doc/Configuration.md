
Relying Party
-------------
One of the Reliyin Party mage for this OpenID Connect provider is 
(OpenID Connect Relying Party Bundle)[https://github.com/waldo2188/OpenIdConnectRelyingPartyBundle]

URIs
----

Below all the URI you need to configure a Relying Party : 
 - /authorize - Authorisation endpoint
 - /token - Token endpont
 - /userinfo - Userinfo endpoint
 - /jwk/oicp.jwk - URI for retrieve the Json Web Token

Some implementation details
===========================

Token endpont authentication
----------------------------
As the (documentation)[http://openid.net/specs/openid-connect-core-1_0.html#ClientAuthentication]
shows, they are several authentication mechanism for authent
RP on OP when querying the Token Endpoint.

For now, just `client_secret_basic` is implemented.

Userinfo endpoint
-----------------
For retrieving the enduser's info, the access/refresh token must be set in the 
header as Authorization's parameter.

```http
Authorization: Bearer Nhs-Xzf01s
```


.htaccess
---------

We must add the following code in `.htaccess` file, for handle Authorization bearer
header
```
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
```