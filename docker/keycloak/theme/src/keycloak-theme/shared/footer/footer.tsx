import { Box, Container, Typography } from "@mui/material";
import { blueGrey } from "@mui/material/colors";
import { ExternalLinks } from "../constants";
import Logo from "../../assets/logo-dark.svg";

import {
  ButtonGroup,
  FooterDivider,
  LogoLink,
  Root,
  TopFooterWrapper,
} from "./footer.styled";
import { footerLinks } from "./footer.constants";
import FooterLinkList from "./components/link-list";

const Footer = () => {
  const pathByAuth = ExternalLinks.main.path;

  return (
    <Root>
      <Container maxWidth="xl">
        <TopFooterWrapper>
          <Box
            display="flex"
            flexDirection="column"
            gap={4}
            alignSelf="stretch"
            flex="1 0 0"
          >
            <LogoLink href={pathByAuth}>
              <Logo />
            </LogoLink>
            <ButtonGroup>{/* {renderCTAButton()} */}</ButtonGroup>
          </Box>
          <Box display="flex" flexDirection="column" gap={2} width={200}>
            <FooterLinkList links={footerLinks.column_1} />
          </Box>
          <Box display="flex" flexDirection="column" gap={2} width={160}>
            <FooterLinkList links={footerLinks.column_2} />
          </Box>
          <Box display="flex" flexDirection="column" gap={2} width={130}>
            <FooterLinkList links={footerLinks.column_3} />
          </Box>
        </TopFooterWrapper>
        <FooterDivider />
        <Box
          marginTop={2.5}
          display="flex"
          gap={1}
          justifyContent="space-between"
          sx={{
            flexDirection: { xs: "column", md: "row" },
            color: "white",
          }}
        >
          <FooterLinkList links={footerLinks.bottom} />
          <Typography variant="body2" color={blueGrey[400]}>
            rights_reserved 2024
          </Typography>
        </Box>
      </Container>
    </Root>
  );
};

export default Footer;
