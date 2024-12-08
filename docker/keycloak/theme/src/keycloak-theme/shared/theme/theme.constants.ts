import { alpha, ThemeOptions } from '@mui/material';
import { createStylesOverride } from './theme.utils';

export const typographyStyles = {
  htmlFontSize: 16,
  fontFamily: ['Inter', 'Arial', 'sans-serif'].join(','),
  body1: {
    fontSize: '16px',
    fontWeight: '400',
    lineHeight: '24px',
  },
  body2: {
    fontSize: '14px',
    fontWeight: '400',
    lineHeight: '20px',
  },
  subtitle1: {
    fontSize: '22px',
    fontWeight: '500',
    lineHeight: '28px',
  },
  subtitle2: {
    fontSize: '16px',
    fontWeight: '500',
    lineHeight: '24px',
  },
  subtitle3: {
    fontSize: '14px',
    fontWeight: '500',
    lineHeight: '20px',
  },
  subtitle4: {
    fontSize: '12px',
    fontWeight: '500',
    lineHeight: '16px',
  },
  overline: {
    fontSize: '12px',
    fontWeight: '400',
    lineHeight: '32px',
    letterSpacing: '1px',
  },
  caption: {
    fontSize: '12px',
    fontWeight: '400',
    lineHeight: '16px',
  },
  h6: {
    fontSize: '24px',
    fontWeight: '500',
    lineHeight: '32px',
    letterSpacing: '0.15px',
  },
  h5: {
    fontSize: '28px',
    fontWeight: '500',
    lineHeight: '36px',
  },
  h4: {
    fontSize: '32px',
    fontWeight: '500',
    lineHeight: '40px',
  },
  h3: {
    fontSize: '36px',
    fontWeight: '600',
    lineHeight: '44px',
  },
  h2: {
    fontSize: '44px',
    fontWeight: '600',
    lineHeight: '52px',
    letterSpacing: '-0.5px',
  },
  h1: {
    fontSize: '56px',
    fontWeight: '600',
    lineHeight: '64px',
    letterSpacing: '-1px',
  },
};

export const typographyVariants = {
  body1: 'p',
  body2: 'p',
  subtitle1: 'p',
  subtitle2: 'p',
  subtitle3: 'p',
  overline: 'p',
  caption: 'p',
  h6: 'h6',
  h5: 'h5',
  h4: 'h4',
  h3: 'h3',
  h2: 'h2',
  h1: 'h1',
};

export const palette = {
  type: 'light',
  common: {
    black: '#000000DE',
    white: '#fff',
  },
  text: {
    primary: '#000000DE',
    secondary: '#666666',
    disabled: '#9e9e9e',
  },
  primary: {
    main: '#1B4EB2',
    light: '#4871C1',
    dark: '#12367C',
    contrastText: '#fff',
    deep: '#151B2C',
    50: '#E4EAF6',
  },
  secondary: {
    main: '#9C27B0',
    dark: '#7B1FA2',
    light: '#BA68C8',
  },
  error: {
    main: '#D32F2F',
    light: '#EF5350',
    dark: '#C62828',
  },
  warning: {
    main: '#EF6C00',
    dark: '#EF6C00',
    light: '#FF9800',
  },
  info: {
    main: '#0288D1',
    dark: '#01579B',
    light: '#03A9F4',
  },
  success: {
    main: '#2E7D32',
    dark: '#1B5E20',
    light: '#4CAF50',
  },
  action: {
    active: '#707070',
    hover: '#f5f5f5',
    selected: '#ebebeb',
    disabledBackground: '#e0e0e0',
    focus: '#e0e0e0',
    disabled: '#9e9e9e',
  },
};

export const themeOptions: ThemeOptions = {
  palette,
  typography: typographyStyles,
  components: {
    MuiTypography: {
      defaultProps: {
        variantMapping: typographyVariants,
      },
    },
    ...createStylesOverride('MuiDialogActions', {
      padding: 0,
      gap: '16px',
      '.MuiButton-root': {
        margin: '0!important',
        flex: '1 1 50%',
      },
    }),
    ...createStylesOverride('MuiDateCalendar', {
      width: 'auto',
      maxWidth: '320px',
      height: 'auto',
      '.MuiYearCalendar-root': {
        maxWidth: '152px',
      },
    }),
    ...createStylesOverride('MuiDialogContent', {
      padding: '24px 0!important',
      margin: 0,
    }),
    ...createStylesOverride('MuiDialogTitle', {
      padding: 0,
      marginBottom: '12px',
    }),
    ...createStylesOverride('MuiDialog', {
      '.MuiDialog-paper': {
        maxWidth: '552px',
        width: '100%',
        padding: '32px',
        borderRadius: '16px',
      },
    }),
    ...createStylesOverride('MuiCardContent', {
      padding: '20px!important',
    }),
    ...createStylesOverride('MuiCard', {
      boxShadow: 'none',
      borderRadius: '16px',
    }),
    ...createStylesOverride('MuiTextField', {
      width: '100%',
    }),
    ...createStylesOverride('MuiTableCell', {
      padding: '3px 4px',
      height: '41px',
      border: 'none',
    }),
    ...createStylesOverride('MuiTableRow', {
      '&:not(&:last-child)': {
        borderBottom: '1px solid #0000001F',
      },
    }),
    MuiChip: {
      styleOverrides: {
        colorPrimary: ({ ownerState }) => ({
          backgroundColor:
            ownerState.variant === 'filled' ? '#BBCAE8' : 'inherit',
          color: palette.common.black,
        }),
        colorSuccess: ({ ownerState }) => ({
          backgroundColor:
            ownerState.variant === 'filled'
              ? alpha(palette.success.main, 0.3)
              : 'inherit',
          color: palette.success.dark,
        }),
        colorError: ({ ownerState }) => ({
          backgroundColor:
            ownerState.variant === 'filled'
              ? alpha(palette.error.main, 0.3)
              : 'inherit',
          color: palette.error.dark,
        }),
        colorWarning: ({ ownerState }) => ({
          backgroundColor:
            ownerState.variant === 'filled'
              ? alpha(palette.warning.main, 0.08)
              : 'inherit',
          color: '#663C00',
        }),
        root: {
          gap: '6px',
          borderRadius: '100px',
          '.MuiChip-icon': {
            margin: 0,
          },
          '.MuiChip-label': {
            padding: 0,
            fontSize: '13px',
            fontWeight: 400,
            lineHeight: '18px',
          },
        },
      },
      variants: [
        {
          props: { variant: 'filled', color: 'default' },
          style: {
            backgroundColor: palette.primary[50],
            color: palette.common.black,
          },
        },
        {
          props: { variant: 'outlined', color: 'default' },
          style: {
            color: palette.text.secondary,
          },
        },
        {
          props: { size: 'medium' },
          style: {
            padding: '7px 10px',
          },
        },
        {
          props: { size: 'small' },
          style: {
            padding: '3px 10px',
          },
        },
      ],
    },
    MuiTooltip: {
      styleOverrides: {
        tooltip: {
          fontSize: '14px',
          fontWeight: 500,
          lineHeight: '20px',
        },
      },
    },
    MuiButton: {
      styleOverrides: {
        root: {
          textTransform: 'initial',
          borderRadius: '32px',
          fontWeight: '500',
          boxShadow: 'none',
        },
      },
      variants: [
        {
          props: { size: 'large' },
          style: {
            padding: '13px 24px',
            fontSize: '14px',
            lineHeight: '22px',
          },
        },
        {
          props: { size: 'medium' },
          style: {
            padding: '10px 16px',
            fontSize: '12px',
            lineHeight: '20px',
          },
        },
        {
          props: { size: 'small' },
          style: {
            padding: '7px 12px',
            fontSize: '11px',
            lineHeight: '18px',
          },
        },
      ],
    },
  },
};
