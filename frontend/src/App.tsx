import React from "react";
import { Navbar } from "./components/Navbar";
import { Box, CssBaseline, MuiThemeProvider } from "@material-ui/core";
import { BrowserRouter } from "react-router-dom";
import ViewRouter from "./routes/ViewRouter";
import Breadcrumbs from "./components/Breadcrumbs";
import theme from "./theme";
import { SnackbarProvider } from "./components/SnackbarProvider";

const App = () => {
  return (
    <React.Fragment>
      <MuiThemeProvider theme={theme}>
        <SnackbarProvider>
          <CssBaseline />
          <BrowserRouter>
            <Navbar />
            <Box paddingTop={"70px"}>
              <Breadcrumbs />
              <ViewRouter />
            </Box>
          </BrowserRouter>
        </SnackbarProvider>
      </MuiThemeProvider>
    </React.Fragment>
  );
};

export default App;
