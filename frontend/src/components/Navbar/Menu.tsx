import React, { ReactElement } from "react";
import { IconButton, Menu as MuiMenu, MenuItem } from "@material-ui/core";
import MenuIcon from "@material-ui/icons/Menu";
import routes, { appRouteProps } from "../../routes";
import { Link } from "react-router-dom";

const listRoutes: any = {
  dashboard: "Dashboard",
  categories: "Categorias",
  castMembers: "Membros de Elenco",
  genres: "Dashboard",
};
const menuRoutes = routes.filter((route) =>
  Object.keys(listRoutes).includes(route.name)
);

const Menu = (): ReactElement => {
  const [anchorEl, setAnchorEl] = React.useState(null);
  const open = Boolean(anchorEl);
  const handleOpen = (event: any) => setAnchorEl(event.currentTarget);
  const handleClose = () => setAnchorEl(null);

  return (
    <React.Fragment>
      <IconButton
        color="inherit"
        aria-label="Open drawer"
        aria-controls="menu-appbar"
        aria-haspopup
        onClick={handleOpen}
      >
        <MenuIcon />
      </IconButton>
      <MuiMenu
        id="menu-appbar"
        open={open}
        anchorEl={anchorEl}
        onClose={handleClose}
        anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
        transformOrigin={{ vertical: "top", horizontal: "center" }}
        getContentAnchorEl={null}
      >
        {Object.keys(listRoutes).map((routeName, key) => {
          const route = menuRoutes.find(
            (route) => route.name === routeName
          ) as appRouteProps;
          return (
            <MenuItem
              key={key}
              component={Link}
              to={route.path as string}
              onClick={handleClose}
            >
              {listRoutes[routeName]}
            </MenuItem>
          );
        })}
      </MuiMenu>
    </React.Fragment>
  );
};

export default Menu;
