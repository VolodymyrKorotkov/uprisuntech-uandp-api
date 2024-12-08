import { FC, FocusEvent, MouseEvent, useRef, useState } from "react";
import {
  Box,
  Button,
  Grow,
  Link,
  MenuItem,
  MenuList,
  Paper,
  Popper,
} from "@mui/material";
import { ExpandMore } from "@mui/icons-material";

import { useTranslate } from "../TranslateProvider";

export type DropDownItem = {
  link: string;
  label: string;
};

export type DropdownProps = {
  links: DropDownItem[];
};

const Dropdown: FC<DropdownProps> = ({ links }: DropdownProps) => {
  const { i18n } = useTranslate();
  const [anchorEl, setAnchorEl] = useState<null | HTMLElement>(null);

  const timeoutRef = useRef<NodeJS.Timeout | null>(null);

  const isMultipliedList = links.length > 1;
  const [titleLink, ...menuLinks] = links;

  const openMenu = (
    event: MouseEvent<HTMLAnchorElement> | FocusEvent<HTMLAnchorElement>
  ) => {
    setAnchorEl(event.currentTarget);
  };

  const closeMenu = () => {
    if (timeoutRef.current) {
      clearTimeout(timeoutRef.current);
    }
    timeoutRef.current = setTimeout(() => {
      setAnchorEl(null);
    }, 0);
  };

  const handleMenuClose = () => {
    setAnchorEl(null);
  };

  const handleMenuEnter = () => {
    if (timeoutRef.current) {
      clearTimeout(timeoutRef.current);
    }
  };

  return (
    <Box position="relative">
      <Button
        component={Link}
        href={titleLink.link}
        target="_blank"
        color="inherit"
        size="large"
        onFocus={openMenu}
        onBlur={closeMenu}
        onMouseEnter={openMenu}
        onMouseLeave={closeMenu}
        sx={(theme) => ({
          paddingX: "16px",
          ...(isMultipliedList
            ? {
                ".MuiButton-endIcon": {
                  marginLeft: "4px",
                  transitionProperty: "transform",
                  transitionTimingFunction: theme.transitions.easing.easeInOut,
                  transitionDuration: `${theme.transitions.duration.shortest}ms`,
                  transform: `rotate(${anchorEl ? 180 : 0}deg)`,
                },
              }
            : {}),
        })}
        endIcon={isMultipliedList ? <ExpandMore /> : null}
      >
        {i18n?.msg(titleLink.label)}
      </Button>
      {isMultipliedList && (
        <Popper
          transition
          disablePortal
          anchorEl={anchorEl}
          open={!!anchorEl}
          placement="bottom-start"
          onFocus={handleMenuEnter}
          onBlur={handleMenuClose}
          onMouseEnter={handleMenuEnter}
          onMouseLeave={handleMenuClose}
          style={{ minWidth: "100%" }}
        >
          {({ TransitionProps }) => (
            <Grow {...TransitionProps}>
              <Paper elevation={8}>
                <MenuList>
                  {menuLinks.map((item, idx) => (
                    <MenuItem
                      key={idx}
                      component={Link}
                      href={item.link}
                      target="_blank"
                    >
                      {i18n?.msg(item.label)}
                    </MenuItem>
                  ))}
                </MenuList>
              </Paper>
            </Grow>
          )}
        </Popper>
      )}
    </Box>
  );
};
export default Dropdown;
