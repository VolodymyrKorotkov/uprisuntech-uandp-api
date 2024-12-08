import React, { useState } from "react";
import { Menu, MenuItem } from "@mui/material";
import { ExpandMore } from "@mui/icons-material";
import { LanguageButton } from "./language-select.styled";

const LanguageSelect = ({
  languages,
  lang,
  handleChangeLocale,
}: {
  lang: string;
  handleChangeLocale: (lang: string) => void;
  languages?: {
    label: string;
    languageTag: string;
    url: string;
  }[];
}) => {
  const [anchorEl, setAnchorEl] = useState<null | HTMLElement>(null);
  const open = Boolean(anchorEl);

  const openMenu = (event: React.MouseEvent<HTMLButtonElement>) =>
    setAnchorEl(event.currentTarget);

  const closeMenu = () => setAnchorEl(null);

  const handleChangeLanguage = (code: string) => () => {
    handleChangeLocale(code);
  };

  return (
    <>
      <LanguageButton
        className={open ? "expanded" : ""}
        id="basic-button"
        size="large"
        aria-haspopup="true"
        aria-controls={open ? "language-list" : undefined}
        aria-expanded={open}
        onClick={openMenu}
        endIcon={<ExpandMore />}
      >
        {lang}
      </LanguageButton>
      <Menu
        id="language-list"
        anchorEl={anchorEl}
        open={open}
        onClose={closeMenu}
      >
        {languages?.map(({ label, languageTag }) => (
          <MenuItem
            key={languageTag}
            onClick={handleChangeLanguage(languageTag)}
          >
            {label}
          </MenuItem>
        ))}
      </Menu>
    </>
  );
};

export default LanguageSelect;
