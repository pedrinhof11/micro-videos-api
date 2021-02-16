import { IconButton, makeStyles, Theme } from "@material-ui/core";
import CloseIcon from "@material-ui/icons/Close";
import {
  SnackbarProvider as NotistackProvider,
  SnackbarProviderProps,
} from "notistack";
import React from "react";

const useStyles = makeStyles((theme: Theme) => ({
  variantSuccess: {
    backgroundColor: theme.palette.success.main,
  },
  variantError: {
    backgroundColor: theme.palette.error.main,
  },
  variantInfo: {
    backgroundColor: theme.palette.primary.main,
  },
}));

export const SnackbarProvider: React.FC<SnackbarProviderProps> = (props) => {
  let snackbarProviderRef: NotistackProvider | null;
  const classes = useStyles();
  const defaultProps: SnackbarProviderProps = {
    classes,
    autoHideDuration: 3000,
    maxSnack: 3,
    anchorOrigin: {
      horizontal: "right",
      vertical: "top",
    },
    ref: (el) => (snackbarProviderRef = el),
    action: (key) => {
      <IconButton
        color={"inherit"}
        style={{ fontSize: 20 }}
        onClick={() => snackbarProviderRef?.closeSnackbar(key)}
      >
        <CloseIcon />
      </IconButton>;
    },
    children: undefined,
  };
  const mergedProps = { ...defaultProps, ...props };
  return (
    <NotistackProvider {...mergedProps}>{props.children}</NotistackProvider>
  );
};
