import { createUseI18n } from "keycloakify/login";

type TranslationMessages = {
  [key: string]: string;
};

export type Translations = {
  [lang: string]: TranslationMessages;
};

export const translations: Translations = {
  en: {
    alphanumericalCharsOnly: "Only alphanumerical characters",
    gender: "Gender",
    doForgotPassword: "I forgot my password",
    invalidUserMessage:
      "Invalid username or password. (this message was overwrite in the theme)",
    "external-links:green_u": "GreenU",
    "external-links:knowledge_base": "Knowledge base",
    "external-links:case_studies": "Case Studies",
    "external-links:interactive_tools": "Interactive tools",
    "external-links:training_platform": "Training Platform",
    "external-links:events": "Events",
    "external-links:documents": "Documents",
    "external-links:solutions": "Solutions",
    "external-links:categories": "Categories",
    "external-links:vendors": "Vendors",
    "external-links:other": "Other",
    "external-links:financing": "Financing",
    "external-links:news": "News",
    "external-links:about": "About",
    "external-links:contacts": "Contacts",
    "external-links:terms": "Terms of Use",
    "external-links:municipality": "Municipality",
  },
};

export type TranslationKeys = keyof TranslationMessages;

export const { useI18n } = createUseI18n(translations);

export type I18n = NonNullable<ReturnType<typeof useI18n>>;
