import { Box, Fab } from "@material-ui/core";
import AddIcon from "@material-ui/icons/Add";
import React from "react";
import { Link } from "react-router-dom";
import Page from "../../components/Page";
import GenresTable from "./GenresTable";

interface Props {}

const Genres = (props: Props) => {
  return (
    <Page title="Listagem de Gêneros">
      <Box dir="rtl" paddingBottom={2}>
        <Fab
          title="Adicionar Gênero"
          size="small"
          color="secondary"
          component={Link}
          to="/genres/create"
        >
          <AddIcon />
        </Fab>
      </Box>
      <Box>
        <GenresTable />
      </Box>
    </Page>
  );
};

export default Genres;
