import { alpha, Divider, styled, Link as RouterLink } from "@mui/material";

export const Root = styled("footer")(({ theme }) => ({
  position: "relative",
  paddingTop: theme.spacing(5),
  paddingBottom: theme.spacing(2.5),
  backgroundColor: theme.palette.primary.deep,
  overflow: "hidden",
  [theme.breakpoints.down("md")]: {
    paddingTop: theme.spacing(2.5),
  },
  "&::after": {
    position: "absolute",
    content: '""',
    height: "300px",
    width: "300px",
    top: "-150px",
    right: "-150px",
    zIndex: 1,
    borderRadius: "300px",
    background: theme.palette.primary.main,
    filter: "blur(187.5px)",
  },
}));

export const ButtonGroup = styled("div")(({ theme }) => ({
  display: "flex",
  flexDirection: "column",
  maxWidth: "fit-content",
  gap: theme.spacing(1.5),
  [theme.breakpoints.down("md")]: {
    maxWidth: "none",
  },
}));

export const TopFooterWrapper = styled("div")(({ theme }) => ({
  display: "flex",
  gap: theme.spacing(3),
  marginBottom: theme.spacing(3),
  color: theme.palette.common.white,
  [theme.breakpoints.down("md")]: {
    flexDirection: "column",
    gap: theme.spacing(4),
  },
}));

export const LogoLink = styled(RouterLink)(({ theme }) => ({
  width: "fit-content",
  svg: {
    height: "80px",
    width: "225px",
    [theme.breakpoints.down("md")]: {
      width: "135px",
      height: "48px",
    },
  },
}));

export const FooterDivider = styled(Divider)(({ theme }) => ({
  backgroundColor: alpha(theme.palette.primary.main, 0.3),
}));
