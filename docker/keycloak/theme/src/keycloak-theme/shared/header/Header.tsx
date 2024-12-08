import { Box, Container } from "@mui/material";

import { navItems } from "../constants";
import { LanguageSelect } from "../language-select";
import { Dropdown } from "../dropdown";

import Logo from "../../assets/logo.svg";

import { Content, DesktopNav, LogoLink, Root } from "./header.styled";

export const Header = ({
  internationalizationEnabled,
  languages,
  lang,
  handleChangeLocale,
}: {
  handleChangeLocale: (lang: string) => void;
  internationalizationEnabled: boolean;
  lang: string;
  languages?: {
    label: string;
    languageTag: string;
    url: string;
  }[];
}) => {
  return (
    <Root>
      <Container maxWidth="xl">
        <Content>
          <LogoLink href={"/"}>
            <Logo />
          </LogoLink>
          <DesktopNav>
            {navItems.map(({ items, id }) => (
              <Dropdown key={id} links={items} />
            ))}
          </DesktopNav>

          <Box
            display="flex"
            gap="4px"
            alignItems="center"
            justifyContent="flex-end"
            whiteSpace="nowrap"
          >
            {internationalizationEnabled && (
              <LanguageSelect
                handleChangeLocale={handleChangeLocale}
                languages={languages}
                lang={lang}
              />
            )}
          </Box>
        </Content>
      </Container>
    </Root>
  );
};
