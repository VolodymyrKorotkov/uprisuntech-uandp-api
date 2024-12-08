import { FC, PropsWithChildren, ReactNode } from "react";
import { Card, SxProps, Theme, Typography } from "@mui/material";
import { CardHeaderWrapper, Root } from "./card-page-layout.styled";

interface CardHeaderProps
  extends PropsWithChildren<Partial<Record<"title" | "subtitle", ReactNode>>> {
  sx?: SxProps<Theme>;
}

export const CardPageWrapper: FC<PropsWithChildren> = ({ children }) => (
  <Root>
    <Card sx={{ maxWidth: "466px", width: "100%" }}>{children}</Card>
  </Root>
);

export const CardHeader: FC<CardHeaderProps> = ({
  children,
  title,
  subtitle,
  sx,
}) => {
  return (
    <CardHeaderWrapper sx={sx}>
      {title && (
        <Typography sx={{ typography: { xs: "h6", md: "h4" } }}>
          {title}
        </Typography>
      )}
      {subtitle && (
        <Typography
          sx={{
            maxWidth: { xs: "326px", md: "408px" },
            typography: { xs: "body2", md: "body1" },
            paddingBottom: { xs: 2, md: 3 },
          }}
        >
          {subtitle}
        </Typography>
      )}
      {children}
    </CardHeaderWrapper>
  );
};
