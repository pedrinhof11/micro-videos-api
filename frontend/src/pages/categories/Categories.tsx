import * as React from 'react';
import Page from "../../components/Page";
import {Box, Fab} from "@material-ui/core";
import {Link} from "react-router-dom";
import AddIcon from "@material-ui/icons/Add";
import CategoriesTable from "./CategoriesTable";

const Categories = () => {
  return (
    <Page title="Listagem de Categorias">
      <Box dir="rtl">
        <Fab
          title="Adicionar Categoria"
          size="small"
          component={Link}
          to="/categories/create"
        >
          <AddIcon/>
        </Fab>
      </Box>
      <Box>
        <CategoriesTable/>
      </Box>
    </Page>
  );
};

export default Categories;