import { createPageStory } from "../createPageStory";

const { PageStory } = createPageStory({ pageId: "register.ftl" });

const meta = {
  title: "login/Register",
  component: PageStory,
  parameters: {
    viewMode: "story",
    previewTabs: {
      "storybook/docs/panel": {
        hidden: true,
      },
    },
  },
};

export default meta;

export const Default = () => <PageStory />;
