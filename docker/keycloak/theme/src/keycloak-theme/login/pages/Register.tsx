import type { PageProps } from "keycloakify/login/pages/PageProps";
import { useGetClassName } from "keycloakify/login/lib/useGetClassName";
import type { KcContext } from "../kcContext";
import type { I18n } from "../i18n";

import { Button, Tabs, CardContent, Box, TextField } from "@mui/material";
import { TabLabel } from "./shared/tab-label";
import { CardHeader } from "./shared/card-page-layout";

export default function Register(
  props: PageProps<Extract<KcContext, { pageId: "register.ftl" }>, I18n>
) {
  const { kcContext, i18n, doUseDefaultCss, Template, classes } = props;

  const { getClassName } = useGetClassName({
    doUseDefaultCss,
    classes,
  });

  const {
    url,
    register,
    realm,
    passwordRequired,
    recaptchaRequired,
    recaptchaSiteKey,
  } = kcContext;

  const { msg, msgStr } = i18n;

  return (
    <Template
      {...{ kcContext, i18n, doUseDefaultCss, classes }}
      headerNode={msg("registerTitle")}
    >
      <CardHeader title={<>UANDP {msg("Account")}</>}>
        <Tabs value={1} variant="fullWidth" sx={{ width: "100%" }}>
          <TabLabel href={url.loginUrl} label={msg("doLogIn")} />
          <TabLabel href={""} label={msg("doRegister")} />
        </Tabs>
      </CardHeader>

      <CardContent>
        <Box
          id="kc-register-form"
          gap={2}
          display="flex"
          flexDirection="column"
          width="100%"
          alignItems="flex-end"
          action={url.registrationAction}
          method="post"
          component="form"
        >
          <TextField
            type="text"
            label={msg("firstName")}
            id="firstName"
            className={getClassName("kcInputClass")}
            name="firstName"
            defaultValue={register.formData.firstName ?? ""}
          />

          <TextField
            type="text"
            label={msg("lastName")}
            id="lastName"
            className={getClassName("kcInputClass")}
            name="lastName"
            defaultValue={register.formData.lastName ?? ""}
          />

          <TextField
            type="text"
            id="email"
            label={msg("email")}
            className={getClassName("kcInputClass")}
            name="email"
            defaultValue={register.formData.email ?? ""}
            autoComplete="email"
          />

          {!realm.registrationEmailAsUsername && (
            <TextField
              type="text"
              id="username"
              label={msg("username")}
              className={getClassName("kcInputClass")}
              name="username"
              defaultValue={register.formData.username ?? ""}
              autoComplete="username"
            />
          )}

          {passwordRequired && (
            <>
              <TextField
                type="password"
                id="password"
                label={msg("password")}
                className={getClassName("kcInputClass")}
                name="password"
                autoComplete="new-password"
              />

              <TextField
                type="password"
                label={msg("passwordConfirm")}
                id="password-confirm"
                className={getClassName("kcInputClass")}
                name="password-confirm"
              />
            </>
          )}

          {recaptchaRequired && (
            <div className="form-group">
              <div className={getClassName("kcInputWrapperClass")}>
                <div
                  className="g-recaptcha"
                  data-size="compact"
                  data-sitekey={recaptchaSiteKey}
                ></div>
              </div>
            </div>
          )}

          <Box
            id="kc-form-buttons"
            display="flex"
            justifyContent="space-between"
            alignContent="space-between"
            width={"100%"}
          >
            <Button
              type="submit"
              children={msgStr("doRegister")}
              fullWidth
              variant="contained"
              size="large"
            />
          </Box>
        </Box>
      </CardContent>
    </Template>
  );
}
