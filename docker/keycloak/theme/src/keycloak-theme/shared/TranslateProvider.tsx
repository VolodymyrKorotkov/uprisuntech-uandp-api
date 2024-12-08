import { createContext, useContext, FC, PropsWithChildren } from "react";
import type { I18n } from "../login/i18n";

interface i18nContext {
  i18n?: I18n;
}

const TranslateContext = createContext<i18nContext>({});

export const TranslateProvider: FC<PropsWithChildren<{ i18n: I18n }>> = ({
  children,
  i18n,
}) => {
  return (
    <TranslateContext.Provider value={{ i18n }}>
      {children}
    </TranslateContext.Provider>
  );
};

export const useTranslate = () => useContext(TranslateContext);
