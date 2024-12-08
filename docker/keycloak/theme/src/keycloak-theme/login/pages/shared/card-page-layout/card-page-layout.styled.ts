import { Box, styled } from '@mui/material';

export const Root = styled(Box)(({ theme }) => ({
  width: '100%',
  minHeight: '100%',
  flex: 1,
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  padding: '71px 0',
  [theme.breakpoints.down('md')]: {
    padding: '16px 0',
  },
}));

export const CardHeaderWrapper = styled('div')(({ theme }) => ({
  padding: '24px 24px 0',
  textAlign: 'center',
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  gap: '16px',
  background: theme.palette.primary[50],
  [theme.breakpoints.down('md')]: {
    padding: '16px 16px 0',
    gap: '8px',
  },
}));
