import { CSSObject } from '@mui/material';

export const createStylesOverride = (componentName: string, css: CSSObject) => {
  return {
    [componentName]: {
      styleOverrides: {
        root: css,
      },
    },
  };
};
