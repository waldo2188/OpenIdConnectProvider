OpendID Connect Provider
========================

- [Install OpenId Connect Provider](#Install)
- [Configure the OpenId Connect Provider](#configure)
- [Manage clients applications](client.md)


# <a id="Install"></a> Install

This project need NodeJs and [Lesscss](http://lesscss.org/) to compile the CSS.

If you use Apache2 as HTTP server, you MUST turn on mod_rewrite !

Clone this project from Github
```bash
    git clone git@github.com:waldo2188/OpenIdConnectProvider.git
    cd OpenIdConnectRelyingParty
```

Install the dependencies with [composer](https://getcomposer.org/)
```bash
    composer install
```

#<a id="configure"></a> Configure the OpenId Connect Provider

In the `app/config/parameters.yml`
```yml
    issuer: 'http://localhost/OpenIdConnectProvider/web/app_dev.php'
    ldap_host: 127.0.0.1
```
 
 - `issuer` is the full URI where the OpenId Connect Provider application is hosted.
 - `ldap_host` is the IP of and LDAP provider. Only if you store enduser accounts in a LDAP.

