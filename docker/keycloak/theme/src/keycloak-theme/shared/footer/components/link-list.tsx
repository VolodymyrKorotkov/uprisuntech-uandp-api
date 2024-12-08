import { FC } from "react";
import { Link, LinkProps } from "@mui/material";
import { useTranslate } from "../../TranslateProvider";

const FooterLinkList: FC<{ links: LinkProps[] }> = ({ links }) => {
  const { i18n } = useTranslate();

  return (
    <>
      {links.map(({ children = "", ...props }, idx) => (
        <Link
          key={idx}
          color="inherit"
          width="fit-content"
          underline="hover"
          target="_blank"
          {...props}
        >
          {i18n?.msg(typeof children === "string" ? children : "")}
        </Link>
      ))}
    </>
  );
};

export default FooterLinkList;
