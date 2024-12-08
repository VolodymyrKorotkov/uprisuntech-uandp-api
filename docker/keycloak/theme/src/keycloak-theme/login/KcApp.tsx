import "./KcApp.css";
import { lazy, Suspense } from "react";
import Fallback, { type PageProps } from "keycloakify/login";
import type { KcContext } from "./kcContext";
import { useI18n } from "./i18n";
import Template from "./Template";

const Login = lazy(() => import("./pages/Login"));
const Register = lazy(() => import("./pages/Register"));
// const Terms = lazy(() => import("./pages/Terms"));
// const Info = lazy(() => import("keycloakify/login/pages/Info"));

const classes = {
  kcHtmlClass: "root",
  kcHeaderWrapperClass: "",
} satisfies PageProps["classes"];

export default function KcApp(props: { kcContext: KcContext }) {
  const { kcContext } = props;

  const i18n = useI18n({ kcContext });

  if (i18n === null) {
    return null;
  }

  return (
    <Suspense>
      {(() => {
        switch (kcContext.pageId) {
          case "login.ftl":
            return (
              <Login
                {...{ kcContext, i18n, Template, classes }}
                doUseDefaultCss={true}
              />
            );
          case "register.ftl":
            return (
              <Register
                {...{ kcContext, i18n, Template, classes }}
                doUseDefaultCss={true}
              />
            );
          // case "terms.ftl":
          //   return (
          //     <Terms
          //       {...{ kcContext, i18n, Template, classes }}
          //       doUseDefaultCss={true}
          //     />
          //   );
          // case "info.ftl":
          //   return (
          //     <Info
          //       {...{ kcContext, i18n, classes }}
          //       Template={lazy(() => import("keycloakify/login/Template"))}
          //       doUseDefaultCss={true}
          //     />
          //   );
          default:
            return (
              <Fallback
                {...{ kcContext, i18n, classes }}
                Template={Template}
                doUseDefaultCss={true}
              />
            );
        }
      })()}
    </Suspense>
  );
}
