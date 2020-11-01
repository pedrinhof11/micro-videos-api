import React, { ReactElement } from 'react'
import { IconButton, Menu as MuiMenu, MenuItem} from "@material-ui/core";
import MenuIcon from "@material-ui/icons/Menu";
import routes, {appRouteProps} from "../../routes";
import {Link} from "react-router-dom";

const listRoutes = [
    "dashboard",
    "categories",
    "castMembers",
    "genres"
];
const menuRoutes = routes.filter( route => listRoutes.includes(route.name));

export default () : ReactElement => {
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
                <MenuIcon/>
            </IconButton>
            <MuiMenu
                id="menu-appbar"
                open={open}
                anchorEl={anchorEl}
                onClose={handleClose}
                anchorOrigin={{vertical: 'bottom', horizontal: "center"}}
                transformOrigin={{vertical: 'top', horizontal: "center"}}
                getContentAnchorEl={null}
            >
                {
                    listRoutes.map((routeName, key) => {
                        const route = menuRoutes.find(route => route.name === routeName) as appRouteProps;
                        return (
                            <MenuItem
                                key={key}
                                component={Link}
                                to={route.path as string}
                                onClick={handleClose}
                            >
                                {route.label}
                            </MenuItem>
                        )
                    })
                }
            </MuiMenu>
        </React.Fragment>
    )
}
