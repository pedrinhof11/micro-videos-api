import * as React from "react";
import {
  AppBar,
  Toolbar,
  Typography,
  Button,
  makeStyles,
  Theme,
} from "@material-ui/core";
import logo from "../../static/img/logo.png";
import Menu from "./Menu";

const useStyles = makeStyles((theme: Theme) => ({
  toolbar: {
    backgroundColor: "#000000",
  },
  title: {
    textAlign: "center",
    flexGrow: 1,
  },
  logo: {
    width: 120,
    [theme.breakpoints.up("sm")]: {
      width: 170,
    },
  },
}));

export const Navbar: React.FC = () => {
  const classes = useStyles();

  return (
    <AppBar>
      <Toolbar className={classes.toolbar}>
        <Menu></Menu>
        <Typography className={classes.title}>
          <img src={logo} alt="CodeFlix" className={classes.logo} />
        </Typography>
        <Button color="inherit">Login</Button>
      </Toolbar>
    </AppBar>
  );
};
