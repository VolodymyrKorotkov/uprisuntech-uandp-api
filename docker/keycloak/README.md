# Keycloak

#### Webhooks provider

| Dependency    | Version                       |
| ------------- | ----------------------------- |
| Java version  | 17-jdk openjdk-17-jre         |
| Maven version | apache-maven-3.6.3-bin.tar.gz |

#### Build command

```bash
./build-providers.sh
```

#### User events

User events are actions that are related to user activities. Keycloak allows you to listen to these events for audit or monitoring purposes. Some of the commonly used user events include:

| Event           | Description                                        |
| --------------- | -------------------------------------------------- |
| LOGIN           | Triggered when a user logs in.                     |
| LOGOUT          | Occurs when a user logs out.                       |
| REGISTER        | Fired when a user registers.                       |
| ACCOUNT_UPDATE  | Triggered when a user updates their account.       |
| PASSWORD_UPDATE | Occurs when a user updates their password.         |
| RESET_PASSWORD  | Fired when a user resets their password.           |
| VERIFY_EMAIL    | Fired when a user verifies their email address.    |
| LOGIN_ERROR     | Triggered when there is a login error.             |
| REGISTER_ERROR  | Occurs when there is an error during registration. |

#### Admin Events

Admin events are triggered by administrative operations. These include operations performed by an admin user through the admin console or the admin REST API. Common admin events include:

| Event  | Description                                                                      |
| ------ | -------------------------------------------------------------------------------- |
| CREATE | Triggered when a resource (user, client, role, etc.) is created.                 |
| UPDATE | Occurs when a resource is updated.                                               |
| DELETE | FiredFired when a resource is deleted.                                           |
| ACTION | TriggeredGeneral actions that don’t necessarily map directly to CRUD operations. |

### Keycloak envs

| ENV                      | Description                                                                                                                                                                                                                                                                  |
| ------------------------ | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| KC_HTTP_ENABLED          | When set to true, this enables HTTP access to the Keycloak instance. This is useful in development environments but should be used with caution in production, where HTTPS is recommended for security.                                                                      |
| KC_HOSTNAME_STRICT       | Determines whether Keycloak enforces strict hostname validation. Setting this to false means that Keycloak does not enforce hostname validation, which can be useful in development environments or when behind a proxy that manages SSL termination.                        |
| KC_HOSTNAME_STRICT_HTTPS | This specifically controls the strict hostname validation for HTTPS connections. When set to false, it allows more flexibility in accepting different hostnames in SSL/TLS certificates, which is particularly useful during testing or when using self-signed certificates. |

### Templates

| Description   | link                                                                                |
| ------------- | ----------------------------------------------------------------------------------- |
| Base template | https://github.com/keycloak/keycloak/tree/main/themes/src/main/resources/theme/base |
