import { Button, styled } from '@mui/material';

export const LanguageButton = styled(Button)(({ theme }) => ({
  paddingLeft: '20px',
  paddingRight: '20px',
  '.MuiButton-endIcon': {
    transitionProperty: 'transform',
    transitionTimingFunction: theme.transitions.easing.easeInOut,
    transitionDuration: `${theme.transitions.duration.shortest}ms`,
  },
  '&.expanded .MuiButton-endIcon': {
    transform: 'rotate(180deg)',
  },
}));
