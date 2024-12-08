import { CSSProperties } from 'react';

export interface CustomTypographyVariants {
  subtitle3: CSSProperties;
  subtitle4: CSSProperties;
}

export interface CustomTypographyPropsVariantOverrides {
  subtitle3: true;
  subtitle4: true;
}

export interface PrimaryColorOverrides {
  deep?: string;
  50?: string;
}
