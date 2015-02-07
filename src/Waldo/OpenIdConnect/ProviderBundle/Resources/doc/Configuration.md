Relying Party
-------------
One of the Relyin Party working with this OpenID Connect provider is 
(OpenID Connect Relying Party Bundle)[https://github.com/waldo2188/OpenIdConnectRelyingPartyBundle]

URIs
----

Below all the URIs you need to configure a Relying Party : 
 - /authorize - Authorization endpoint
 - /token - Token endpont
 - /userinfo - Userinfo endpoint
 - /jwk/oicp.jwk - URI to retrieve the Json Web Token

Some implementation details
===========================

Token endpont authentication
----------------------------
As the (documentation)[http://openid.net/specs/openid-connect-core-1_0.html#ClientAuthentication]
shows, there are several authentication mechanism for authent
RP on OP when querying the Token Endpoint.

In this implementation, just `client_secret_basic` is implemented.

Userinfo endpoint
-----------------
To retrieve the enduser's info, the access/refresh token must be set in the 
header as Authorization's parameter.

The Apache2's `mod_rewrite` MUST be active !

```http
Authorization: Bearer Nhs-Xzf01s
```


.htaccess
---------

We must add the following code in `.htaccess` file, to handle Authorization bearer in the
header
```
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
```
