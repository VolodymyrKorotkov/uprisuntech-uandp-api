import { useEffect } from "react";
import { clsx } from "keycloakify/tools/clsx";
import { usePrepareTemplate } from "keycloakify/lib/usePrepareTemplate";
import { type TemplateProps } from "keycloakify/login/TemplateProps";
import { useGetClassName } from "keycloakify/login/lib/useGetClassName";
import type { KcContext } from "./kcContext";
import type { I18n } from "./i18n";
import { Box, CssBaseline, GlobalStyles } from "@mui/material";
import { ThemeProvider } from "../shared/theme";
import { alpha, styled, Alert } from "@mui/material";
import { Header } from "../shared/header";
import { Footer } from "../shared/footer";
import { globalStyles } from "../shared/globalStyles";
import { TranslateProvider } from "../shared/TranslateProvider";
import { CardPageWrapper } from "./pages/shared/card-page-layout";

const Root = styled("div")(({ theme }) => ({
  display: "flex",
  flexDirection: "column",
  minHeight: "100vh",
  background: alpha(theme.palette.primary.main, 0.04),
}));

export default function Template(props: TemplateProps<KcContext, I18n>) {
  const {
    displayInfo = false,
    displayMessage = true,
    displayWide = false,
    showAnotherWayIfPresent = true,
    infoNode = null,
    kcContext,
    i18n,
    doUseDefaultCss,
    classes,
    children,
  } = props;

  const { getClassName } = useGetClassName({ doUseDefaultCss, classes });

  const { msg, changeLocale, labelBySupportedLanguageTag, currentLanguageTag } =
    i18n;

  const { realm, locale, auth, url, message } = kcContext;

  const { isReady } = usePrepareTemplate({
    doFetchDefaultThemeResources: doUseDefaultCss,
    htmlClassName: getClassName("kcHtmlClass"),
    bodyClassName: getClassName("kcBodyClass"),
    htmlLangProperty: locale?.currentLanguageTag,
    documentTitle: i18n.msgStr("loginTitle", kcContext.realm.displayName),
  });

  useEffect(() => {
    console.log(
      `Value of MY_ENV_VARIABLE on the Keycloak server: "${kcContext.properties.MY_ENV_VARIABLE}"`
    );
  }, []);

  if (!isReady) {
    return null;
  }

  const handleChangeLocale = (lang: string) => changeLocale(lang);

  return (
    <>
      <CssBaseline />
      <ThemeProvider>
        <GlobalStyles styles={globalStyles} />
        <TranslateProvider i18n={i18n}>
          <Box bgcolor="common.white" style={{ height: "100%" }}>
            <Root>
              <Header
                internationalizationEnabled={realm.internationalizationEnabled}
                languages={
                  locale !== undefined && locale.supported.length
                    ? locale.supported
                    : undefined
                }
                handleChangeLocale={handleChangeLocale}
                lang={labelBySupportedLanguageTag[currentLanguageTag]}
              />

              <CardPageWrapper>
                {children}

                {displayMessage && message !== undefined && (
                  <Box sx={{ padding: "0px 18px 8px" }}>
                    <Alert severity={message.type}>{message.summary}</Alert>
                  </Box>
                )}

                {auth !== undefined &&
                  auth.showTryAnotherWayLink &&
                  showAnotherWayIfPresent && (
                    <form
                      id="kc-select-try-another-way-form"
                      action={url.loginAction}
                      method="post"
                      className={clsx(
                        displayWide && getClassName("kcContentWrapperClass")
                      )}
                    >
                      <div
                        className={clsx(
                          displayWide && [
                            getClassName("kcFormSocialAccountContentClass"),
                            getClassName("kcFormSocialAccountClass"),
                          ]
                        )}
                      >
                        <div className={getClassName("kcFormGroupClass")}>
                          <input
                            type="hidden"
                            name="tryAnotherWay"
                            value="on"
                          />
                          <a
                            href="#"
                            id="try-another-way"
                            onClick={() => {
                              document.forms[
                                "kc-select-try-another-way-form" as never
                              ].submit();
                              return false;
                            }}
                          >
                            {msg("doTryAnotherWay")}
                          </a>
                        </div>
                      </div>
                    </form>
                  )}

                {displayInfo && (
                  <div id="kc-info" className={getClassName("kcSignUpClass")}>
                    <div
                      id="kc-info-wrapper"
                      className={getClassName("kcInfoAreaWrapperClass")}
                    >
                      {infoNode}
                    </div>
                  </div>
                )}
              </CardPageWrapper>
              <Footer />
            </Root>
          </Box>
        </TranslateProvider>
      </ThemeProvider>
    </>
  );
}
