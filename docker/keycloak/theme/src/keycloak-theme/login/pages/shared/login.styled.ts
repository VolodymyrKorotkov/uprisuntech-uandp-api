import { styled, Tab, Button } from "@mui/material";

export const TabLabel = styled(Tab)(() => ({
  textTransform: "unset",
  fontSize: "12px",
  fontWeight: 500,
  lineHeight: "20px",
}));

export const SocialWrapper = styled("div")(({ theme }) => ({
  display: "flex",
  flexDirection: "column",
  textAlign: "center",
  gap: "16px",
  alignItems: "center",
  marginBottom: "16px",
  [theme.breakpoints.down("md")]: {
    marginTop: "16px",
  },
  width: "100%",
}));

export const SocialButton = styled(Button)(({ theme }) => ({
  background: theme.palette.common.black,
  fontWeight: 500,
  width: "100%",
  color: "white",
  "@keyframes granimate": {
    "0%, 100%": {
      backgroundPosition: "0%, 25%",
    },
    "25%, 75% ": {
      backgroundPosition: "50%, 50%",
    },
    "50%": {
      backgroundPosition: "100%, 100%",
    },
  },
  "&:hover": {
    transition: "all .1s linear",
    animation: "10s infinite granimate",
    background: "transparent",
    color: theme.palette.common.black,
    backgroundSize: "250% 250%",
    backgroundImage:
      "linear-gradient(217deg,rgba(255,0,0,.8),rgba(255,0,0,0) 70.71%),linear-gradient(127deg,rgba(0,0,255,.8),rgba(0,0,255,0) 70.71%),linear-gradient(336deg,rgba(0,255,0,.8),rgba(0,255,0,0) 70.71%)",
  },
}));
