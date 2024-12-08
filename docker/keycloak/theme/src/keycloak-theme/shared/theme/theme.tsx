import {
  createTheme,
  ThemeProvider as CustomThemeProvider,
} from "@mui/material";
import { themeOptions } from "./theme.constants";
import {
  CustomTypographyPropsVariantOverrides,
  CustomTypographyVariants,
  PrimaryColorOverrides,
} from "./theme.types";

declare module "@mui/material/styles" {
  interface TypographyVariants extends CustomTypographyVariants {}

  interface TypographyVariantsOptions extends CustomTypographyVariants {}

  interface PaletteColor extends PrimaryColorOverrides {}

  interface SimplePaletteColorOptions extends PrimaryColorOverrides {}
}

declare module "@mui/material/Typography" {
  interface TypographyPropsVariantOverrides
    extends CustomTypographyPropsVariantOverrides {}
}

export const theme = createTheme(themeOptions);

export default function ThemeProvider({
  children,
}: {
  children: string | JSX.Element | JSX.Element[];
}) {
  return <CustomThemeProvider theme={theme}>{children}</CustomThemeProvider>;
}
