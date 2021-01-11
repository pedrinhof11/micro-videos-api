import { Box, Fab } from "@material-ui/core";
import AddIcon from "@material-ui/icons/Add";
import React from "react";
import { Link } from "react-router-dom";
import Page from "../../components/Page";
import CastMembersTable from "./CastMembersTable";

interface Props {}

const CastMembers = (props: Props) => {
  return (
    <Page title="Listagem de Membros de Elenco">
      <Box dir="rtl" paddingBottom={2}>
        <Fab
          title="Adicionar Membro de Elenco"
          size="small"
          color="secondary"
          component={Link}
          to="/cast-members/create"
        >
          <AddIcon />
        </Fab>
      </Box>
      <Box>
        <CastMembersTable />
      </Box>
    </Page>
  );
};

export default CastMembers;
