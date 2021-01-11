import { Box, Fab } from "@material-ui/core";
import AddIcon from "@material-ui/icons/Add";
import * as React from "react";
import { Link } from "react-router-dom";
import Page from "../../components/Page";
import CategoriesTable from "./CategoriesTable";

const Categories = () => {
  return (
    <Page title="Listagem de Categorias">
      <Box dir="rtl" paddingBottom={2}>
        <Fab
          title="Adicionar Categoria"
          size="small"
          color="secondary"
          component={Link}
          to="/categories/create"
        >
          <AddIcon />
        </Fab>
      </Box>
      <Box>
        <CategoriesTable />
      </Box>
    </Page>
  );
};

export default Categories;
