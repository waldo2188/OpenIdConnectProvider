OpendID Connect Provider
========================

OpenID Connect Provider is an implementation of [OpenID Connect Specification](http://openid.net/specs/openid-connect-core-1_0.html).
This is a standalone application based on Symfony framework.

This application is functional, but really far away to be released with a stable version.
If you want to join the party, be my guest !

##What next ?

- [Read the documentation](app/Resources/doc/index.md)


##TODO

- Rebase with the original [Gree/JOSE](https://github.com/gree/jose)
- Add the ability to the JOSE_JWKMaker class to create an array of JWK and a functionnality for adding a new key
- When signing an ID Token or Enduserinfo with an RSA alg, we must add, in the header of JWT, the jku (uri to the OP jwk) and kid used for signing.
- Why not https://github.com/nelmio/NelmioSecurityBundle ?
