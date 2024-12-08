const VITE_WP_BACKEND_HOST = "https://stage-wp.uprisun.dev";

export const ExternalLinks = {
  main: {
    name: "",
    path: `${VITE_WP_BACKEND_HOST}`,
  },
  green_u: {
    name: "external-links:green_u",
    path: `${VITE_WP_BACKEND_HOST}/green-university`,
  },
  knowledge_base: {
    name: "external-links:knowledge_base",
    path: `${VITE_WP_BACKEND_HOST}/knowledge-base`,
  },
  case_studies: {
    name: "external-links:case_studies",
    path: `${VITE_WP_BACKEND_HOST}/cases`,
  },
  interactive_tools: { name: "external-links:interactive_tools", path: "" },
  training_platform: {
    name: "external-links:training_platform",
    path: `${VITE_WP_BACKEND_HOST}/training-center`,
  },
  events: { name: "external-links:events", path: "" },
  documents: {
    name: "external-links:documents",
    path: `${VITE_WP_BACKEND_HOST}/documents`,
  },
  solutions: {
    name: "external-links:solutions",
    path: `${VITE_WP_BACKEND_HOST}/marketplace`,
  },
  categories: {
    name: "external-links:categories",
    path: `${VITE_WP_BACKEND_HOST}/categories`,
  },
  vendors: {
    name: "external-links:vendors",
    path: `${VITE_WP_BACKEND_HOST}/vendors-list`,
  },
  other: { name: "external-links:other", path: "" },
  financing: {
    name: "external-links:financing",
    path: `${VITE_WP_BACKEND_HOST}/financing`,
  },
  news: {
    name: "external-links:news",
    path: `${VITE_WP_BACKEND_HOST}/news`,
  },
  about: {
    name: "external-links:about",
    path: `${VITE_WP_BACKEND_HOST}/about-us`,
  },
  contacts: {
    name: "external-links:contacts",
    path: `${VITE_WP_BACKEND_HOST}/contacts`,
  },
  terms: {
    name: "external-links:terms",
    path: `${VITE_WP_BACKEND_HOST}/privacy-policy`,
  },
  municipality: {
    name: "external-links:municipality",
    path: `${VITE_WP_BACKEND_HOST}/municipality/`,
  },
};

export const navItems = [
  {
    id: "GreenU",
    items: [
      {
        link: ExternalLinks.green_u.path,
        label: ExternalLinks.green_u.name,
      },
      {
        link: ExternalLinks.knowledge_base.path,
        label: ExternalLinks.knowledge_base.name,
      },
      {
        link: ExternalLinks.case_studies.path,
        label: ExternalLinks.case_studies.name,
      },
      {
        link: ExternalLinks.training_platform.path,
        label: ExternalLinks.training_platform.name,
      },
      {
        link: ExternalLinks.documents.path,
        label: ExternalLinks.documents.name,
      },
    ],
  },
  {
    id: "Новини",
    items: [
      {
        link: ExternalLinks.news.path,
        label: ExternalLinks.news.name,
      },
    ],
  },
  {
    id: "Фінансування",
    items: [
      {
        link: ExternalLinks.financing.path,
        label: ExternalLinks.financing.name,
      },
      {
        link: ExternalLinks.municipality.path,
        label: ExternalLinks.municipality.name,
      },
    ],
  },
  {
    id: "Рішення",
    items: [
      {
        link: ExternalLinks.solutions.path,
        label: ExternalLinks.solutions.name,
      },
      {
        link: ExternalLinks.categories.path,
        label: ExternalLinks.categories.name,
      },
      {
        link: ExternalLinks.vendors.path,
        label: ExternalLinks.vendors.name,
      },
    ],
  },
  {
    id: "Про платформу",
    items: [
      {
        link: ExternalLinks.about.path,
        label: ExternalLinks.about.name,
      },
      {
        link: ExternalLinks.contacts.path,
        label: ExternalLinks.contacts.name,
      },
    ],
  },
];
