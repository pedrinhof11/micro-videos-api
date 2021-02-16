import { Box, CssBaseline, MuiThemeProvider } from "@material-ui/core";
import * as React from "react";
import { BrowserRouter } from "react-router-dom";
import Breadcrumbs from "./components/Breadcrumbs";
import Navbar from "./components/Navbar";
import { SnackbarProvider } from "./components/SnackbarProvider";
import ViewRouter from "./routes/ViewRouter";
import theme from "./theme";

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
