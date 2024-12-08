import { createRoot } from "react-dom/client";
import { StrictMode, lazy, Suspense } from "react";
import { kcContext as kcLoginThemeContext } from "./keycloak-theme/login/kcContext";

const LoginTheme = lazy(() => import("./keycloak-theme/login/KcApp"));

createRoot(document.getElementById("root")!).render(
  <StrictMode>
    <Suspense>
      {(() => {
        if (kcLoginThemeContext !== undefined) {
          return <LoginTheme kcContext={kcLoginThemeContext} />;
        }
      })()}
    </Suspense>
  </StrictMode>
);
