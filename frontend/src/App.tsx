import React from 'react';
import {Navbar} from "./components/Navbar";
import {Box, CssBaseline, MuiThemeProvider} from '@material-ui/core';
import {BrowserRouter} from "react-router-dom";
import ViewRouter from "./routes/ViewRouter";
import Breadcrumbs from "./components/Breadcrumbs";
import theme from "./theme";

const App = () => {
  return (
    <React.Fragment>
      <MuiThemeProvider theme={theme}>
        <CssBaseline/>
        <BrowserRouter>
          <Navbar/>
          <Box paddingTop={'70px'}>
            <Breadcrumbs/>
            <ViewRouter/>
          </Box>
        </BrowserRouter>
      </MuiThemeProvider>
    </React.Fragment>
  );
}

export default App;
