import { LinkProps } from "@mui/material";
import { ExternalLinks } from "../constants";

export const footerLinks: { [key: string]: LinkProps[] } = {
  column_1: [
    {
      variant: "subtitle2",
      children: ExternalLinks.green_u.name,
      href: ExternalLinks.green_u.path,
    },
    {
      variant: "body1",
      children: ExternalLinks.knowledge_base.name,
      href: ExternalLinks.knowledge_base.path,
    },
    {
      variant: "body1",
      children: ExternalLinks.case_studies.name,
      href: ExternalLinks.case_studies.path,
    },
    {
      variant: "body1",
      children: ExternalLinks.interactive_tools.name,
      href: ExternalLinks.interactive_tools.path,
    },
    {
      variant: "body1",
      children: ExternalLinks.training_platform.name,
      href: ExternalLinks.training_platform.path,
    },
    {
      variant: "body1",
      children: ExternalLinks.events.name,
      href: ExternalLinks.events.path,
    },
    {
      variant: "body1",
      children: ExternalLinks.documents.name,
      href: ExternalLinks.documents.path,
    },
  ],
  column_2: [
    {
      variant: "subtitle2",
      children: ExternalLinks.solutions.name,
      href: ExternalLinks.solutions.path,
    },
    {
      variant: "body1",
      children: ExternalLinks.categories.name,
      href: ExternalLinks.categories.path,
    },
    {
      variant: "body1",
      children: ExternalLinks.vendors.name,
      href: ExternalLinks.vendors.path,
    },
  ],
  column_3: [
    {
      variant: "subtitle2",
      children: ExternalLinks.other.name,
      href: ExternalLinks.other.path,
    },
    {
      variant: "body1",
      children: ExternalLinks.financing.name,
      href: ExternalLinks.financing.path,
    },
    {
      variant: "body1",
      children: ExternalLinks.news.name,
      href: ExternalLinks.news.path,
    },
    {
      variant: "body1",
      children: ExternalLinks.about.name,
      href: ExternalLinks.about.path,
    },
    {
      variant: "body1",
      children: ExternalLinks.contacts.name,
      href: ExternalLinks.contacts.path,
    },
  ],
  bottom: [
    {
      variant: "body1",
      children: ExternalLinks.terms.name,
      href: ExternalLinks.terms.path,
    },
  ],
};
