```plantuml
Security -> Guard: getCredentials
Security -> Guard: getPrincipal
Security -> Guard: checkCredentials
Security -> Guard: createAuthenticatedRequestToken
```


```plantuml
Security -> JwtGuard: getCredentials
JwtGuard --> Security: AuthenticationRequest(jwt)
Security -> JwtGuard: getPrincipal
JwtGuard --> Security: JwtToken
Security -> JwtGuard: checkCredentials
JwtGuard --> JwtGuard: verify signature 
Security -> FormLoginGuard: createAuthenticatedRequestToken
JwtGuard --> Security: AuthenticatedRequest(JwtToken)
```

```plantuml
Security -> FormLoginGuard: getCredentials
FormLoginGuard --> Security: AuthenticationRequest(username, password)
Security -> FormLoginGuard: getPrincipal
FormLoginGuard --> Security: DomainUser
Security -> FormLoginGuard: checkCredentials
FormLoginGuard --> FormLoginGuard: user.password.equals(token.password) 
Security -> FormLoginGuard: createAuthenticatedRequestToken
FormLoginGuard --> Security: AuthenticatedRequest(User)
```
