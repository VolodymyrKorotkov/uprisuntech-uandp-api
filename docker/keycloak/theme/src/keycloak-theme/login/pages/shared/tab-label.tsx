import { styled, Tab, TabProps } from "@mui/material";
import { FC } from "react";

interface TabLabelProps extends TabProps {
  href: string;
}

export const TabLabel: FC<TabLabelProps> = styled(Tab)(() => ({
  textTransform: "unset",
  fontSize: "12px",
  fontWeight: 500,
  lineHeight: "20px",
}));
