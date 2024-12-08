import { Box, styled, Link } from "@mui/material";

export const Root = styled("header")(({ theme }) => ({
  width: "100%",
  position: "sticky",
  top: 0,
  backgroundColor: theme.palette.common.white,
  zIndex: theme.zIndex.drawer + 1,
  paddingTop: theme.spacing(1.5),
  paddingBottom: theme.spacing(1.5),
  [theme.breakpoints.down("md")]: {
    paddingTop: theme.spacing(1),
    paddingBottom: theme.spacing(1),
  },
}));

export const Content = styled("div")(({ theme }) => ({
  padding: "0 20px",
  display: "grid",
  alignItems: "center",
  gridTemplateColumns:
    "minmax(147px, 243px) minmax(auto, 1fr) minmax(306px, 403px)",
  gap: theme.spacing(1),
  border: `1px solid ${theme.palette.primary[50]}`,
  borderRadius: "16px",
  [theme.breakpoints.down("lg")]: {
    gridTemplateColumns:
      "minmax(147px, 243px) minmax(auto, 1fr) minmax(124px, 230px)",
  },
  [theme.breakpoints.down("md")]: {
    padding: "0 16px",
    display: "flex",
    justifyContent: "space-between",
  },
}));

export const LogoLink = styled(Link)(({ theme }) => ({
  padding: "10px 0",
  display: "flex",
  svg: {
    height: "52px",
    width: "147px",
    [theme.breakpoints.down("md")]: {
      width: "135px",
      height: "48px",
    },
  },
}));
export const DesktopNav = styled(Box)(({ theme }) => ({
  height: "100%",
  display: "flex",
  alignItems: "center",
  justifyContent: "center",
  borderRight: `1px solid ${theme.palette.primary[50]}`,
  borderLeft: `1px solid ${theme.palette.primary[50]}`,
  [theme.breakpoints.down("md")]: {
    display: "none",
  },
}));
