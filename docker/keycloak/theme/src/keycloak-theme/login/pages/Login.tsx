import { useState, type FormEventHandler } from "react";
import { useConstCallback } from "keycloakify/tools/useConstCallback";
import type { PageProps } from "keycloakify/login/pages/PageProps";
import type { KcContext } from "../kcContext";
import type { I18n } from "../i18n";
import {
  TextField,
  Button,
  Tabs,
  CardContent,
  Box,
  Link,
  Checkbox,
  FormGroup,
  FormControlLabel,
} from "@mui/material";
import { CardHeader } from "./shared/card-page-layout";
import { TabLabel } from "./shared/tab-label";
import { SocialButton, SocialWrapper } from "./shared/login.styled";

const queryUsername = new URL(window.location.href).searchParams.get(
  "username"
);

export default function Login(
  props: PageProps<Extract<KcContext, { pageId: "login.ftl" }>, I18n>
) {
  const { kcContext, i18n, doUseDefaultCss, Template, classes } = props;

  const {
    social,
    realm,
    url,
    usernameHidden,
    login,
    auth,
    registrationDisabled,
  } = kcContext;

  const [isEmailLogin, setIsEmailLogin] = useState<boolean>(
    !social?.providers?.length
  );

  const { msg, msgStr } = i18n;

  const [isLoginButtonDisabled, setIsLoginButtonDisabled] = useState(false);

  const onSubmit = useConstCallback<FormEventHandler<HTMLFormElement>>((e) => {
    e.preventDefault();

    setIsLoginButtonDisabled(true);

    const formElement = e.target as HTMLFormElement;

    // NOTE: If we login with email, Keycloak expects username and password in the POST request.
    formElement
      .querySelector("input[name='email']")
      ?.setAttribute("name", "username");

    formElement.submit();
  });

  return (
    <Template
      {...{ kcContext, i18n, doUseDefaultCss, classes }}
      displayInfo={
        realm.password && realm.registrationAllowed && !registrationDisabled
      }
      displayWide={realm.password && social.providers !== undefined}
      headerNode={""}
    >
      <CardHeader title={<>UANDP {msg("Account")}</>}>
        <Tabs value={0} variant="fullWidth" sx={{ width: "100%" }}>
          <TabLabel href="" label={msg("doLogIn")} />
          <TabLabel href={url.registrationUrl} label={msg("doRegister")} />
        </Tabs>
      </CardHeader>

      <CardContent>
        {realm.password && social.providers !== undefined && (
          <SocialWrapper>
            {social.providers.map((p) => (
              <SocialButton id={`zocial-${p.alias}`} href={p.loginUrl}>
                Via {p.displayName}
              </SocialButton>
            ))}
          </SocialWrapper>
        )}

        {!isEmailLogin && (
          <Button
            onClick={() => setIsEmailLogin(true)}
            fullWidth
            variant="text"
            size="large"
          >
            {"Via Email"}
          </Button>
        )}

        {isEmailLogin && (
          <Box
            gap={2}
            display="flex"
            flexDirection="column"
            width="100%"
            alignItems="flex-end"
          >
            {realm.password && (
              <Box
                component="form"
                gap={2}
                display="flex"
                flexDirection="column"
                width="100%"
                alignItems="flex-end"
                action={url.loginAction}
                method="post"
                onSubmit={onSubmit}
                id="kc-form-login"
              >
                {!usernameHidden &&
                  (() => {
                    const label = !realm.loginWithEmailAllowed
                      ? "username"
                      : realm.registrationEmailAsUsername
                        ? "email"
                        : "usernameOrEmail";

                    const autoCompleteHelper: typeof label =
                      label === "usernameOrEmail" ? "username" : label;

                    return (
                      <>
                        <TextField
                          tabIndex={1}
                          id={autoCompleteHelper}
                          label={msg(label)}
                          //NOTE: This is used by Google Chrome auto fill so we use it to tell
                          //the browser how to pre fill the form but before submit we put it back
                          //to username because it is what keycloak expects.
                          name={autoCompleteHelper}
                          defaultValue={queryUsername || login.username || ""}
                          type="text"
                          autoFocus={true}
                          autoComplete="off"
                        />
                      </>
                    );
                  })()}

                <TextField
                  label={msg("password")}
                  tabIndex={2}
                  id="password"
                  name="password"
                  type="password"
                  autoComplete="off"
                />

                <Box
                  display="flex"
                  flexDirection="row"
                  width="100%"
                  alignItems="center"
                  justifyContent={"space-between"}
                >
                  {realm.rememberMe && !usernameHidden && (
                    <FormGroup>
                      <FormControlLabel
                        control={
                          <Checkbox
                            tabIndex={2}
                            id="rememberMe"
                            name="rememberMe"
                            {...(login.rememberMe === "on"
                              ? {
                                  checked: true,
                                }
                              : {})}
                          />
                        }
                        label={msg("rememberMe")}
                      />
                    </FormGroup>
                  )}

                  {realm.resetPasswordAllowed && (
                    <Link
                      color="primary"
                      width="fit-content"
                      underline="hover"
                      variant="body2"
                      href={url.loginResetCredentialsUrl}
                      tabIndex={5}
                    >
                      {msg("doForgotPassword")}
                    </Link>
                  )}
                </Box>

                <input
                  type="hidden"
                  id="id-hidden-input"
                  name="credentialId"
                  {...(auth?.selectedCredential !== undefined
                    ? {
                        value: auth.selectedCredential,
                      }
                    : {})}
                />
                <Button
                  tabIndex={4}
                  name="login"
                  id="kc-login"
                  type="submit"
                  children={msgStr("doLogIn")}
                  fullWidth
                  variant="contained"
                  size="large"
                  disabled={isLoginButtonDisabled}
                />
              </Box>
            )}
          </Box>
        )}
      </CardContent>
    </Template>
  );
}
