import { Box, Container } from "@material-ui/core";
import MuiBreadcrumbs from "@material-ui/core/Breadcrumbs";
import Link, { LinkProps } from "@material-ui/core/Link";
import { createStyles, makeStyles, Theme } from "@material-ui/core/styles";
import Typography from "@material-ui/core/Typography";
import React from "react";
import { Route } from "react-router";
import { Link as RouterLink } from "react-router-dom";
import RouteParser from "route-parser";
import routes from "../routes";

/* eslint-disable no-nested-ternary */

const breadcrumbNameMap: { [key: string]: string } = {};
routes.forEach((route) => {
  breadcrumbNameMap[route.path as string] = route.label;
});

const useStyles = makeStyles((theme: Theme) =>
  createStyles({
    root: {
      display: "flex",
      flexDirection: "column",
    },
    linkRouter: {
      color: "#4db5ab",
      "&:focus, &:active": {
        color: "#4db5ab",
      },
      "&:hover": {
        color: "#055a52",
        textDecoration: "none",
      },
    },
  })
);

interface LinkRouterProps extends LinkProps {
  to: string;
  replace?: boolean;
}

const LinkRouter = (props: LinkRouterProps) => (
  <Link {...props} component={RouterLink as any} />
);

export default function Breadcrumbs() {
  const classes = useStyles();
  function makeBreadcrumb(location: Location) {
    const pathnames = location.pathname.split("/").filter((x) => x);
    pathnames.unshift("/");
    return (
      <MuiBreadcrumbs aria-label="breadcrumb">
        {pathnames.map((value, index) => {
          const last = index === pathnames.length - 1;
          const to = `${pathnames
            .slice(0, index + 1)
            .join("/")
            .replace("//", "/")}`;
          const route = Object.keys(breadcrumbNameMap).find((path) =>
            new RouteParser(path).match(to)
          );
          if (!route) {
            return false;
          }
          return last ? (
            <Typography color="textPrimary" key={to}>
              {breadcrumbNameMap[route]}
            </Typography>
          ) : (
            <LinkRouter
              color="inherit"
              to={to}
              key={to}
              className={classes.linkRouter}
            >
              {breadcrumbNameMap[route]}
            </LinkRouter>
          );
        })}
      </MuiBreadcrumbs>
    );
  }
  return (
    <Container className={classes.root}>
      <Box paddingTop={2} paddingBottom={1}>
        <Route>
          {({ location }: { location: Location }) => makeBreadcrumb(location)}
        </Route>
      </Box>
    </Container>
  );
}
