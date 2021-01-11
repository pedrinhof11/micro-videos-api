import React, { ReactElement } from "react";
import { Container, Typography, makeStyles, Box } from "@material-ui/core";

const useStyles = makeStyles({
  title: {
    color: "#999999",
  },
});

type PageProps = {
  title: String;
  children?: any;
};
export default function Page({ title, children }: PageProps): ReactElement {
  const classes = useStyles();
  return (
    <Container>
      <Typography className={classes.title} component="h1" variant="h5">
        {title}
      </Typography>
      <Box paddingTop={1}>{children}</Box>
    </Container>
  );
}
