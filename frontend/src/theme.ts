import {createMuiTheme} from "@material-ui/core";

const palette = {
  primary: {
    main: "#79aec8",
      contrastText: "#fff"
  },
  secondary: {
    main: "#4db5ab",
      contrastText: "#fff"
  },
  background: {
  default: "#fafafa"
  }
};

const theme = createMuiTheme({
  palette,
  overrides: {
    MUIDataTable: {
      paper: {
        boxShadow: "none"
      }
    },
    MUIDataTableToolbar: {
      root: {
        minHeight: "58px",
        backgroundColor: palette.background.default
      },
      icon: {
        color: palette.primary.main,
        '&:hover, &:active, &.focus': {
          color: "#055a52"
        }
      },
      iconActive: {
        color: "#055a52",
        '&:hover, &:active, &.focus': {
          color: "#055a52"
        }
      }
    },
    MUIDataTableHeadCell: {
      fixedHeader: {
        paddingTop: 8,
        paddingBottom: 8,
        backgroundColor: palette.primary.main,
        color: "#fff",
        "&[aria-sort]": {
          backgroundColor: "#459ac4"
        }
      },
      sortActive: {
        color: "#fff"
      },
      sortAction: {
        alignItems: 'center'
      },
      sortLabelRoot: {
        "& svg": {
          color: "#fff !important"
        }
      }
    },
    MUIDataTableSelectCell: {
      headerCell: {
        backgroundColor: palette.primary.main,
        "& span": {
          color: "#fff !important"
        }
      }
    },
    MUIDataTableBodyCell: {
      root: {
        color: palette.secondary.main,
        '&:hover, &:active, &.focus': {
          color: palette.secondary.main
        }
      }
    },
    MUIDataTableToolbarSelect: {
      title: {
        color: palette.primary.main
      },
      iconButton: {
        color: palette.primary.main
      }
    },
    MUIDataTableBodyRow: {
      root: {
        "&:nth-child(odd)": {
          background: palette.background.default
        }
      }
    },
    MUIDataTablePagination: {
      root: {
        color: palette.primary.main
      }
    }
  }
});

export default theme;