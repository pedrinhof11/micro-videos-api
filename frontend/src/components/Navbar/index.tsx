import {
  AppBar,
  Button,
  makeStyles,
  Theme,
  Toolbar,
  Typography,
} from "@material-ui/core";
import React, { useRef } from "react";
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

const Navbar = () => {
  const nodeRef = useRef(null);
  const classes = useStyles();

  return (
    <AppBar ref={nodeRef}>
      <Toolbar className={classes.toolbar}>
        <Menu />
        <Typography className={classes.title}>
          <img src={logo} alt="CodeFlix" className={classes.logo} />
        </Typography>
        <Button color="inherit">Login</Button>
      </Toolbar>
    </AppBar>
  );
};
export default Navbar;
